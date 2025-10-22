<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinishedProduct;
use App\Traits\TableFilterTrait;
use App\Models\FinishedBranchStock;
use App\Models\Unit;
use App\Models\Branch;
use App\Models\Category;
use App\Helpers\ImageHelper;
use App\Helpers\CodeGeneratorHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FinishedProductController extends Controller
{
    use TableFilterTrait;
    public function __construct()
    {
        // Constructor without BranchStockService dependency
    }

    public function index(Request $request)
    {
        // Get user's branch and check permissions
        $currentBranch = auth()->check() ? auth()->user()->branch : null;
        $canSwitchBranch = auth()->check() && auth()->user()->is_superadmin;
        
        // PROTECTION: Prevent finished products access from production centers
        // Only allow if user is superadmin or if they're not at a production center
        if ($currentBranch && $currentBranch->type === 'production' && !$canSwitchBranch) {
            $message = 'Akses ke produk siap jual tidak diizinkan dari Pusat Produksi. ' .
                      'Pusat Produksi hanya mengelola bahan mentah dan bahan setengah jadi.';
            
            return redirect()->route('dashboard')
                           ->with('error', $message);
        }
        
        // Determine branch for filtering
        $selectedBranch = null;
        if (request('branch_id')) {
            $selectedBranch = Branch::where('is_active', true)->find(request('branch_id'));
        }
        
        // Determine which branch to use for stock filtering
        $branchForStock = $selectedBranch ?? $currentBranch;
        $showBranchSelector = $canSwitchBranch || !$currentBranch;
        
        // Build the query for finished products
        $query = FinishedProduct::with(['category', 'unit']);

        $columns = [
            ['key' => 'code', 'label' => 'Kode'],
            ['key' => 'photo', 'label' => 'Gambar'],
            ['key' => 'name', 'label' => 'Nama Produk'],
            ['key' => 'category', 'label' => 'Kategori'],
            ['key' => 'unit', 'label' => 'Satuan'],
            ['key' => 'current_stock', 'label' => 'Stok Di Cabang'],
            ['key' => 'minimum_stock', 'label' => 'Stok Minimum'],
            ['key' => 'price', 'label' => 'Harga Jual'],
            ['key' => 'is_active', 'label' => 'Status'],
        ];
        
        // Load finished branch stocks for the specific branch if selected
        if ($branchForStock) {
            $query = $query->with(['finishedBranchStocks' => function($q) use ($branchForStock) {
                $q->where('branch_id', $branchForStock->id)->with('branch');
            }]);
        } else {
            // Load all finished branch stocks if no specific branch
            $query = $query->with(['finishedBranchStocks' => function($q) {
                $q->with('branch');
            }]);
        }
        
        // === SEARCH ===
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($b) => $b->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('unit', fn($r) => $r->where('unit_name', 'like', "%{$search}%"));
            });
        }

        // === FILTER STATUS ===
        if ($status = $request->get('is_active')) {
            $query->where('is_active', $status);
        }

        // === SORTING ===
        if ($sortBy = $request->get('sort_by')) {
            $sortDir = $request->get('sort_dir', 'asc');

            // 🧩 Deteksi kolom relasi
            switch ($sortBy) {
                case 'category':
                    $query->leftjoin('categories', 'categories.id', '=', 'finished_products.category_id')
                        ->orderBy('categories.name', $sortDir)
                        ->select('finished_products.*');
                    break;

                case 'unit':
                    $query->leftjoin('units', 'units.id', '=', 'finished_products.unit_id')
                        ->orderBy('units.unit_name', $sortDir)
                        ->select('finished_products.*');
                    break;

                default:
                    $query->orderBy("finished_products.{$sortBy}", $sortDir);
            }
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $finishedProducts */
        $finishedProducts = $query->paginate(10);

        // Manual sorting untuk current_stock
        if ($sortBy === 'current_stock') {
            $finishedProducts->setCollection(
                $finishedProducts->getCollection()->sortBy(
                    fn($item) => $item->display_stock_quantity,
                    SORT_REGULAR,
                    request('sort_dir') === 'desc'
                )
            );
        }

        $statuses = [
            1 => 'Aktif',
            0 => 'Nonaktif',
        ];

        $selects = [
            [
                'name' => 'is_active',
                'label' => 'Semua Status',
                'options' => $statuses,
            ],
        ];

        // === RESPONSE ===
        if ($request->ajax()) {
            return response()->json([
                'data' => $finishedProducts->items(),
                'links' => (string) $finishedProducts->links('vendor.pagination.tailwind'),
            ]);
        }
        
        // Initialize branch stock for products that don't have it and calculate display stock
        foreach ($finishedProducts as $product) {
            if ($branchForStock) {
                // Initialize stock for specific branch if it doesn't exist
                if ($product->finishedBranchStocks->isEmpty()) {
                    $product->initializeStockForBranch($branchForStock->id);
                    $product->load(['finishedBranchStocks' => function($q) use ($branchForStock) {
                        $q->where('branch_id', $branchForStock->id);
                    }]);
                }
                
                // Calculate display stock for specific branch
                $branchStock = $product->finishedBranchStocks->first();
                $product->display_stock_quantity = $branchStock ? $branchStock->quantity : 0;
            } else {
                // Accumulate stock from all branches
                $product->display_stock_quantity = $product->finishedBranchStocks->sum('quantity');
            }
        }

        return view('finished-products.index', [
            'finishedProducts' => $finishedProducts->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $finishedProducts,
            'selectedBranch' => $selectedBranch,
            'branchForStock' => $branchForStock,
            'showBranchSelector' => $showBranchSelector,
        ]);
    }

    public function create()
    {
        // Get user's branch and check permissions
        $currentBranch = auth()->check() ? auth()->user()->branch : null;
        $canSwitchBranch = auth()->check() && auth()->user()->is_superadmin;
        
        // Determine selected branch from request
        $selectedBranch = null;
        if (request('branch_id')) {
            $selectedBranch = Branch::where('is_active', true)->find(request('branch_id'));
        }
        
        // Use selected branch or current branch for validation
        $branchToCheck = $selectedBranch ?? $currentBranch;
        
        // PROTECTION: Prevent finished product creation at production centers
        if ($branchToCheck && $branchToCheck->type === 'production') {
            $message = 'Produk siap jual tidak dapat dibuat di Pusat Produksi. ' .
                      'Pusat Produksi hanya mengolah bahan mentah menjadi bahan setengah jadi. ' .
                      'Silakan pilih cabang toko untuk membuat produk siap jual.';
            
            // Redirect to retail branches if possible
            $retailBranches = Branch::where('is_active', true)->where('type', 'branch')->first();
            $redirectParams = $retailBranches ? ['branch_id' => $retailBranches->id] : [];
            
            return redirect()->route('finished-products.index', $redirectParams)
                           ->with('error', $message);
        }
        
        $units = Unit::where('is_active', true)->orderBy('unit_name')->get();
        // Get categories specifically for finished products
        $categories = Category::forFinishedProducts()
                             ->where('is_active', true)
                             ->orderBy('name')
                             ->get();

        if ($units->count() == 0) {
            $message = 'Sebelum menambah produk siap jual, Anda wajib mengisi data satuan terlebih dahulu. ' .
                      '<a href="' . route('units.index') . '" class="alert-link">Kelola satuan</a>.';
            // Get branch_id from request or session
            $branchId = request('branch_id') ?: session('selected_branch_id');
            return redirect()->route('finished-products.index', $branchId ? ['branch_id' => $branchId] : [])->with('warning', $message);
        }
        
        // Get user's branch and check permissions
        $currentBranch = auth()->check() ? auth()->user()->branch : null;
        $canSwitchBranch = auth()->check() && auth()->user()->is_superadmin;
        
        // Get all branches for selection
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        
        // Determine selected branch from request
        $selectedBranch = null;
        if (request('branch_id')) {
            $selectedBranch = Branch::where('is_active', true)->find(request('branch_id'));
        }
        
        // Get retail branches only (exclude production centers)
        $retailBranches = Branch::where('is_active', true)
                               ->where('type', 'branch')
                               ->orderBy('name')
                               ->get();
        
        return view('finished-products.create', compact(
            'units', 
            'categories', 
            'branches', 
            'retailBranches',
            'currentBranch',
            'selectedBranch',
            'canSwitchBranch'
        ));
    }

    public function store(Request $request)
    {
        // PROTECTION: Prevent finished product creation at production centers
        $currentBranch = auth()->check() ? auth()->user()->branch : null;
        $selectedBranchId = $request->input('header_branch_id') ?: $request->input('selected_branch_id');
        
        if ($selectedBranchId) {
            $selectedBranch = Branch::find($selectedBranchId);
            if ($selectedBranch && $selectedBranch->type === 'production') {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Produk siap jual tidak dapat dibuat di Pusat Produksi. Pusat Produksi hanya mengolah bahan mentah menjadi bahan setengah jadi.');
            }
        } elseif ($currentBranch && $currentBranch->type === 'production') {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Produk siap jual tidak dapat dibuat di Pusat Produksi. Silakan pilih cabang toko.');
        }
        
        // Debug: Write to custom debug file
        $debugInfo = "=== STORE METHOD DEBUG ===\n";
        $debugInfo .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
        $debugInfo .= "Has photo file: " . ($request->hasFile('photo') ? 'YES' : 'NO') . "\n";
        $debugInfo .= "Request data: " . json_encode($request->all()) . "\n";
        file_put_contents(storage_path('upload_debug.log'), $debugInfo, FILE_APPEND);
        
        // Debug: Log the request data
        Log::info('Store method called', [
            'all_data' => $request->all(),
            'has_photo' => $request->hasFile('photo'),
            'photo_details' => $request->hasFile('photo') ? [
                'name' => $request->file('photo')->getClientOriginalName(),
                'size' => $request->file('photo')->getSize(),
                'mime' => $request->file('photo')->getMimeType()
            ] : null
        ]);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => $request->filled('code') ? 'string|max:50|unique:finished_products,code' : '',
            'description' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'unit_id' => 'required|exists:units,id',
            'category_id' => 'required|exists:categories,id',
            'minimum_stock' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'stock_mode' => 'required|string|in:selected,all',
            'selected_branch_id' => 'nullable|exists:branches,id',
            'header_branch_id' => 'nullable|exists:branches,id',
            'price' => 'required|numeric|min:0',
            // New base_cost input (Modal Dasar) mapped to production_cost in DB
            'base_cost' => 'nullable|numeric|min:0|lte:price',
            'production_cost' => 'nullable|numeric|min:0|lte:price',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Set default values for nullable fields that cannot be null in database
        $validated['minimum_stock'] = !empty($validated['minimum_stock']) ? $validated['minimum_stock'] : 0;
        $validated['stock_quantity'] = $validated['stock_quantity'] ?? 0;
        // Map base_cost (form) to production_cost (DB)
        if (array_key_exists('base_cost', $validated)) {
            $validated['production_cost'] = $validated['base_cost'] ?? 0;
            unset($validated['base_cost']);
        } else {
            $validated['production_cost'] = $validated['production_cost'] ?? 0;
        }

        // Debug: Ensure price has a value
        if (empty($validated['price']) || $validated['price'] === null) {
            $validated['price'] = 0;
        }
        
        // Generate unique code if not provided
        if (!$request->filled('code')) {
            $validated['code'] = CodeGeneratorHelper::generateProductCode('FP', $validated['name'], FinishedProduct::class);
        }

        // Debug: Log file upload status
        Log::info('Photo upload check', [
            'has_photo' => $request->hasFile('photo'),
            'has_image' => $request->hasFile('image'),
            'files' => $request->allFiles()
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            try {
                // Log upload attempt
                Log::info('File upload details', [
                    'original_name' => $request->file('photo')->getClientOriginalName(),
                    'size' => $request->file('photo')->getSize(),
                    'mime_type' => $request->file('photo')->getMimeType()
                ]);
                
                // Store using standardized ImageHelper
                $validated['photo'] = ImageHelper::storeProductImage($request->file('photo'), 'finished');
                
                // Log success
                Log::info('Photo upload success', [
                    'path' => $validated['photo'],
                    'exists' => file_exists(storage_path('app/public/' . $validated['photo']))
                ]);
            } catch (\Exception $e) {
                Log::error('Photo upload exception', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Handle image upload (legacy)
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/materials'), $filename);
            $validated['image'] = 'storage/materials/' . $filename;
        }

        $finishedProduct = FinishedProduct::create($validated);
        
        $debugFinal = "PRODUCT CREATED\n";
        $debugFinal .= "Product ID: " . $finishedProduct->id . "\n";
        $debugFinal .= "Photo field in DB: " . ($finishedProduct->photo ?? 'NULL') . "\n";
        $debugFinal .= "Validated photo: " . ($validated['photo'] ?? 'NOT SET') . "\n";
        $debugFinal .= "===================\n\n";
        file_put_contents(storage_path('upload_debug.log'), $debugFinal, FILE_APPEND);
        
        Log::info('Product created', [
            'product_id' => $finishedProduct->id,
            'photo_field' => $finishedProduct->photo,
            'validated_photo' => $validated['photo'] ?? 'not set'
        ]);

        // Handle stock initialization based on selected mode
        $stockQuantity = $validated['stock_quantity'] ?? 0;
        $minStock = $validated['min_stock'] ?? 0;
        $stockMode = $validated['stock_mode'];
        $selectedBranchId = $validated['selected_branch_id'] ?? null;
        
        if ($stockMode === 'selected' && $selectedBranchId) {
            // Check if selected branch is production center
            $selectedBranch = Branch::find($selectedBranchId);
            if ($selectedBranch && $selectedBranch->type === 'production') {
                // Production centers don't sell finished products, so don't initialize stock
                Log::info('Skipping stock initialization for production center', [
                    'branch_id' => $selectedBranchId,
                    'branch_name' => $selectedBranch->name
                ]);
            } else {
                // Initialize stock for selected retail branch only
                $finishedProduct->initializeStockForBranch($selectedBranchId, $stockQuantity, $minStock);
                Log::info('Stock initialized for selected branch', [
                    'branch_id' => $selectedBranchId,
                    'stock_quantity' => $stockQuantity,
                    'min_stock' => $minStock
                ]);
            }
        } else {
            // Initialize stock for all retail branches (exclude production centers)
            $retailBranches = Branch::where('is_active', true)
                                   ->where('type', 'branch')
                                   ->get();
            
            foreach ($retailBranches as $branch) {
                $finishedProduct->initializeStockForBranch($branch->id, $stockQuantity, $minStock);
            }
            
            Log::info('Stock initialized for all retail branches', [
                'retail_branches_count' => $retailBranches->count(),
                'stock_quantity' => $stockQuantity,
                'min_stock' => $minStock
            ]);
        }

        // Get branch_id from request or session
        // Fix for 'Undefined array key header_branch_id' error
        $branchId = $request->input('header_branch_id', session('selected_branch_id'));
        
        return redirect()->route('finished-products.index', $branchId ? ['branch_id' => $branchId] : [])
            ->with('success', __('messages.product_created'));
    }

    public function show(FinishedProduct $finishedProduct)
    {
        // Get user's branch and check permissions
        $currentBranch = auth()->check() ? auth()->user()->branch : null;
        $canSwitchBranch = auth()->check() && auth()->user()->is_superadmin;
        
        // Determine branch for filtering
        $selectedBranch = null;
        if (request('branch_id')) {
            $selectedBranch = Branch::where('is_active', true)->find(request('branch_id'));
        }
        
        // Determine which branch to use for stock filtering
        $branchForStock = $selectedBranch ?? $currentBranch;
        
        // Get all branches for header selector
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        
        // Load product with relationships
        $finishedProduct->load(['category', 'unit', 'finishedBranchStocks.branch']);
        
        // Get branch-specific stock
        $currentBranchStock = null;
        $displayStockQuantity = 0;
        
        if ($branchForStock) {
            // Get stock for specific branch
            $currentBranchStock = $finishedProduct->finishedBranchStocks->where('branch_id', $branchForStock->id)->first();
            $displayStockQuantity = $currentBranchStock ? $currentBranchStock->quantity : 0;
        } else {
            // Accumulate stock from all branches
            $displayStockQuantity = $finishedProduct->finishedBranchStocks->sum('quantity');
        }
        
        return view('finished-products.show', compact(
            'finishedProduct', 
            'currentBranchStock',
            'displayStockQuantity',
            'branches',
            'selectedBranch',
            'currentBranch',
            'branchForStock',
            'canSwitchBranch'
        ))->with('showBranchSelector', true);
    }

    public function edit(FinishedProduct $finishedProduct)
    {
        // Get user's branch and check permissions
        $currentBranch = auth()->check() ? auth()->user()->branch : null;
        $canSwitchBranch = auth()->check() && auth()->user()->is_superadmin;
        
        // Determine branch for filtering
        $selectedBranch = null;
        if (request('branch_id')) {
            $selectedBranch = Branch::where('is_active', true)->find(request('branch_id'));
        }
        
        // Determine which branch to use for stock filtering
        $branchForStock = $selectedBranch ?? $currentBranch;
        
        // Get all branches for header selector
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        
        // Load product with relationships
        $finishedProduct->load(['category', 'unit', 'finishedBranchStocks.branch']);
        
        // Get branch-specific stock
        $currentBranchStock = null;
        $displayStockQuantity = 0;
        
        if ($branchForStock) {
            // Get stock for specific branch
            $currentBranchStock = $finishedProduct->finishedBranchStocks->where('branch_id', $branchForStock->id)->first();
            $displayStockQuantity = $currentBranchStock ? $currentBranchStock->quantity : 0;
        } else {
            // Accumulate stock from all branches
            $displayStockQuantity = $finishedProduct->finishedBranchStocks->sum('quantity');
        }
        
        $units = Unit::orderBy('unit_name')->get();
        $categories = Category::orderBy('name')->get();
        
        return view('finished-products.edit', compact(
            'finishedProduct', 
            'units', 
            'categories',
            'currentBranchStock',
            'displayStockQuantity',
            'branches',
            'selectedBranch',
            'currentBranch',
            'branchForStock',
            'canSwitchBranch'
        ))->with('showBranchSelector', true);
    }

    public function update(Request $request, FinishedProduct $finishedProduct)
    {
        Log::info('--- FinishedProduct Update Start ---', ['request_data' => $request->all()]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:finished_products,code,' . $finishedProduct->id,
            'description' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'unit_id' => 'required|exists:units,id',
            'category_id' => 'nullable|exists:categories,id',
            'minimum_stock' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'price' => 'required|numeric|min:0',
            // New base_cost input (Modal Dasar) mapped to production_cost in DB
            'base_cost' => 'nullable|numeric|min:0|lte:price',
            'production_cost' => 'nullable|numeric|min:0|lte:price',
            'is_active' => 'boolean',
        ]);

        Log::info('Data after validation', ['validated_data' => $validated]);

        $validated['is_active'] = $request->has('is_active');
        $validated['minimum_stock'] = !empty($validated['minimum_stock']) ? $validated['minimum_stock'] : 0;
        $stockQuantity = $validated['stock_quantity'] ?? 0;
        // Map base_cost (form) to production_cost (DB)
        if (array_key_exists('base_cost', $validated)) {
            $validated['production_cost'] = $validated['base_cost'] ?? 0;
            unset($validated['base_cost']);
        } else {
            $validated['production_cost'] = $validated['production_cost'] ?? 0;
        }
        
        if (empty($validated['price']) || $validated['price'] === null) {
            $validated['price'] = 0;
        }

        Log::info('Data before saving', ['data_to_save' => $validated]);

        // Handle photo upload using ImageHelper
        if ($request->hasFile('photo')) {
            if ($finishedProduct->photo && Storage::disk('public')->exists($finishedProduct->photo)) {
                Storage::disk('public')->delete($finishedProduct->photo);
            }
            $validated['photo'] = \App\Helpers\ImageHelper::storeProductImage($request->file('photo'), 'finished');
        }

        // Remove stock_quantity from main product update since it's managed per branch
        unset($validated['stock_quantity']);
        unset($validated['image']);

        $finishedProduct->update($validated);

        // Update branch-specific stock if stock_quantity was provided and we have a specific branch
        $branchId = $request->input('branch_id') ?: $request->input('header_branch_id') ?: session('selected_branch_id');
        
        if ($branchId && isset($stockQuantity)) {
            // Get or create branch stock record
            $branchStock = $finishedProduct->finishedBranchStocks()->where('branch_id', $branchId)->first();
            
            if (!$branchStock) {
                // Initialize stock for this branch if it doesn't exist
                $finishedProduct->initializeStockForBranch($branchId, $stockQuantity);
                Log::info('Initialized stock for branch', [
                    'branch_id' => $branchId,
                    'stock_quantity' => $stockQuantity
                ]);
            } else {
                // Update existing branch stock
                $branchStock->update(['quantity' => $stockQuantity]);
                Log::info('Updated branch stock', [
                    'branch_id' => $branchId,
                    'old_quantity' => $branchStock->quantity,
                    'new_quantity' => $stockQuantity
                ]);
            }
        }

        Log::info('--- FinishedProduct Update End ---', ['product_after_save' => $finishedProduct->fresh()->toArray()]);

        return redirect()->route('finished-products.index', $branchId ? ['branch_id' => $branchId] : [])
            ->with('success', __('messages.product_updated'));
    }

    public function destroy(FinishedProduct $finishedProduct)
    {
        // Delete photo if exists
        if ($finishedProduct->photo && Storage::disk('public')->exists($finishedProduct->photo)) {
            Storage::disk('public')->delete($finishedProduct->photo);
        }
        
        // Delete legacy image if exists
        if ($finishedProduct->image && file_exists(public_path($finishedProduct->image))) {
            unlink(public_path($finishedProduct->image));
        }
        
        $finishedProduct->delete();

        // Get branch_id from session
        $branchId = session('selected_branch_id');
        
        return redirect()->route('finished-products.index', $branchId ? ['branch_id' => $branchId] : [])
            ->with('success', __('messages.product_deleted'));
    }

    /**
     * Update stock for a finished product in a specific branch
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'finished_product_id' => 'required|exists:finished_products,id',
            'branch_id' => 'required|exists:branches,id',
            'type' => 'required|in:in,out,return',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $finishedProduct = FinishedProduct::findOrFail($request->finished_product_id);
            
            // Update stock using the model method
            $finishedProduct->updateStockForBranch(
                $request->branch_id,
                $request->type,
                $request->quantity,
                $request->notes,
                auth()->id()
            );

            $typeText = [
                'in' => 'Stok Masuk',
                'out' => 'Stok Keluar', 
                'return' => 'Stok Retur'
            ][$request->type];

            return response()->json([
                'success' => true,
                'message' => "{$typeText} berhasil dicatat untuk {$finishedProduct->name}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * API endpoint to get finished products list
     */
    public function apiIndex()
    {
        $finishedProducts = FinishedProduct::with(['unit:id,unit_name,abbreviation'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'unit_id']);

        $result = $finishedProducts->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'unit' => $p->unit ? [
                    'id' => $p->unit->id,
                    'unit_name' => $p->unit->unit_name,
                    'abbreviation' => $p->unit->abbreviation,
                ] : null,
            ];
        });

        return response()->json($result);
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(FinishedProduct $finishedProduct)
    {
        $finishedProduct->update(['is_active' => !$finishedProduct->is_active]);
        $status = $finishedProduct->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('finished-products.index')
                        ->with('success', "Produk jadi berhasil {$status}.");
    }
}
