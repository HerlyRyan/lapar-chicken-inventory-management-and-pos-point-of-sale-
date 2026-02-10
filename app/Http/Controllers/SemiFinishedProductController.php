<?php

namespace App\Http\Controllers;

use App\Models\{SemiFinishedProduct, SemiFinishedBranchStock, Branch, Unit, Category};
use App\Helpers\{ImageHelper, CodeGeneratorHelper};
use App\Traits\TableFilterTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Reverb\Loggers\Log;

class SemiFinishedProductController extends Controller
{
    use TableFilterTrait;
    public function __construct()
    {
        // Constructor without BranchStockService dependency
    }

    public function index(Request $request)
    {
        $branchForStock = null;
        $showBranchSelector = true;

        // Determine branch for filtering
        $selectedBranch = null;
        $currentBranch = auth()->check() ? auth()->user()->branch : null;

        if (session('branch_id')) {
            $selectedBranch = Branch::where('is_active', true)->find(session('branch_id'));
        }

        // dd($selectedBranch);

        // Determine which branch to use for stock filtering
        $branchForStock = $selectedBranch ?? $currentBranch;

        $query = SemiFinishedProduct::with(['unit', 'category']);

        $columns = [
            ['key' => 'code', 'label' => 'Kode'],
            ['key' => 'image', 'label' => 'Gambar'],
            ['key' => 'name', 'label' => 'Nama Bahan'],
            ['key' => 'category', 'label' => 'Kategori'],
            ['key' => 'unit', 'label' => 'Satuan'],
            ['key' => 'current_stock', 'label' => 'Stok Saat Ini'],
            ['key' => 'minimum_stock', 'label' => 'Stok Minimum'],
            ['key' => 'production_cost', 'label' => 'Biaya Produksi'],
            ['key' => 'is_active', 'label' => 'Status'],
        ];

        // Load semiFinished branch stocks for the specific branch if selected
        if ($branchForStock) {
            $query = $query->with(['semiFinishedBranchStocks' => function ($q) use ($branchForStock) {
                $q->where('branch_id', $branchForStock->id)->with('branch');
            }]);
        } else {
            // Load all semiFinished branch stocks if no specific branch
            $query = $query->with(['semiFinishedBranchStocks' => function ($q) {
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
                    $query->leftjoin('categories', 'categories.id', '=', 'semi_finished_products.category_id')
                        ->orderBy('categories.name', $sortDir)
                        ->select('semi_finished_products.*');
                    break;

                case 'unit':
                    $query->leftjoin('units', 'units.id', '=', 'semi_finished_products.unit_id')
                        ->orderBy('units.unit_name', $sortDir)
                        ->select('semi_finished_products.*');
                    break;

                case 'production_cost':
                    $query->orderBy('semi_finished_products.production_cost', $sortDir);
                    break;

                default:
                    $query->orderBy("semi_finished_products.{$sortBy}", $sortDir);
            }
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $semiFinishedProducts */
        $semiFinishedProducts = $query->paginate(10);

        // Manual sorting untuk current_stock
        if ($sortBy === 'current_stock') {
            $semiFinishedProducts->setCollection(
                $semiFinishedProducts->getCollection()->sortBy(
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
                'data' => $semiFinishedProducts->items(),
                'links' => (string) $semiFinishedProducts->links('vendor.pagination.tailwind'),
            ]);
        }

        // Load semi-finished branch stocks for the specific branch if selected
        if ($branchForStock) {
            $query = $query->with(['semiFinishedBranchStocks' => function ($q) use ($branchForStock) {
                $q->where('branch_id', $branchForStock->id)->with('branch');
            }]);
        } else {
            // Load all semi-finished branch stocks if no specific branch
            $query = $query->with(['semiFinishedBranchStocks' => function ($q) {
                $q->with('branch');
            }]);
        }

        // Initialize branch stock for products that don't have it and calculate display stock
        foreach ($semiFinishedProducts as $product) {
            if ($branchForStock) {
                // Initialize stock for specific branch if it doesn't exist
                if ($product->semiFinishedBranchStocks->isEmpty()) {
                    $product->initializeStockForBranch($branchForStock->id);
                    $product->loadMissing(['semiFinishedBranchStocks' => function ($q) use ($branchForStock) {
                        $q->where('branch_id', $branchForStock->id);
                    }]);
                }

                // Calculate display stock for specific branch
                $branchStock = $product->semiFinishedBranchStocks->first();
                $product->display_stock_quantity = $branchStock ? $branchStock->quantity : 0;
            } else {
                // Calculate total stock across all branches
                $product->display_stock_quantity = $product->semiFinishedBranchStocks->sum('quantity');
            }
        }

        return view('semi-finished-products.index', [
            'semiFinishedProducts' => $semiFinishedProducts->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $semiFinishedProducts,
            'selectedBranch' => $selectedBranch,
            'branchForStock' => $branchForStock,
            'showBranchSelector' => $showBranchSelector,
        ]);
    }

    public function create()
    {
        $units = Unit::active()->orderBy('unit_name')->get();

        // Get categories filtered by material type for semi-finished products
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        if ($units->count() == 0) {
            $message = 'Sebelum menambah bahan setengah jadi, Anda wajib mengisi data satuan terlebih dahulu. ' .
                '<a href="' . route('units.index') . '" class="alert-link">Kelola satuan</a>.';
            $branchId = request('branch_id') ?: session('selected_branch_id');
            return redirect()->route('semi-finished-products.index', $branchId ? ['branch_id' => $branchId] : [])->with('warning', $message);
        }

        // Get branch context for stock initialization
        $selectedBranch = null;
        $currentBranch = null;

        // Only set branch if explicitly selected via branch_id parameter
        // If no branch_id is provided, we're in "overview semua cabang" mode
        if (request('branch_id')) {
            $selectedBranch = Branch::where('is_active', true)->find(request('branch_id'));
        }

        // Do NOT automatically use session or user branch for create form
        // This ensures "overview semua cabang" mode works correctly

        // Get all branches for selector
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        // Get permission info
        $canSwitchBranch = auth()->check() && auth()->user()->is_superadmin;

        return view('semi-finished-products.create', compact('units', 'categories', 'selectedBranch', 'currentBranch', 'branches', 'canSwitchBranch'))->with('showBranchSelector', true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => $request->filled('code') ? 'string|max:50|unique:semi_finished_products,code' : '',
            'description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'production_cost' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'stock_mode' => 'nullable|string|in:selected,all',
            'header_branch_id' => 'nullable|exists:branches,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama bahan setengah jadi wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'unit_id.required' => 'Satuan wajib dipilih.',
            'unit_id.exists' => 'Satuan tidak valid.',
            'production_cost.numeric' => 'Biaya produksi harus berupa angka.',
            'production_cost.min' => 'Biaya produksi minimal 0.',
            'minimum_stock.numeric' => 'Stok minimum harus berupa angka.',
            'minimum_stock.min' => 'Stok minimum minimal 0.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/semi-finished'), $filename);
            $validated['image'] = 'storage/semi-finished/' . $filename;
        }

        // Generate unique code if not provided
        if (!$request->filled('code')) {
            $validated['code'] = CodeGeneratorHelper::generateProductCode('SF', $validated['name'], SemiFinishedProduct::class);
        }

        $semiFinishedProduct = SemiFinishedProduct::create($validated);

        // Handle stock initialization
        $stockQuantity = $validated['stock_quantity'] ?? 0;
        $stockMode = $validated['stock_mode'] ?? 'all';
        $branchId = session('branch_id') ?? Auth::user()->branch_id;

        if ($stockQuantity > 0) {
            if ($stockMode === 'selected' && $branchId) {
                // Initialize stock for specific branch only
                $semiFinishedProduct->initializeStockForBranch($branchId, $stockQuantity);
            } else {
                // Initialize stock for all branches (default behavior)
                $branches = Branch::where('is_active', true)->get();
                foreach ($branches as $branch) {
                    $semiFinishedProduct->initializeStockForBranch($branch->id, $stockQuantity);
                }
            }
        } else {
            // Always initialize stock with zero for all active branches
            $branches = Branch::where('is_active', true)->get();
            foreach ($branches as $branch) {
                $semiFinishedProduct->initializeStockForBranch($branch->id, 0);
            }
        }

        return redirect()
            ->route('semi-finished-products.index')
            ->with('success', 'Bahan setengah jadi berhasil ditambahkan.');
    }

    /**
     * Update stock for a semi-finished product in a specific branch
     */
    public function updateStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:semi_finished_products,id',
            'branch_id' => 'required|exists:branches,id',
            'stock_type' => 'required|in:in,out,return',
            'quantity' => 'required|numeric|min:0.01',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = SemiFinishedProduct::findOrFail($validated['product_id']);
        $branch = Branch::findOrFail($validated['branch_id']);

        // Get or create branch stock record
        $branchStock = SemiFinishedBranchStock::firstOrCreate(
            [
                'branch_id' => $validated['branch_id'],
                'semi_finished_product_id' => $validated['product_id']
            ],
            [
                'quantity' => 0,
                'average_cost' => 0 // Not used anymore, using product-level production_cost instead
            ]
        );

        $oldQuantity = $branchStock->quantity;
        $quantityChange = $validated['quantity'];

        // Calculate new quantity based on stock type
        switch ($validated['stock_type']) {
            case 'in':
            case 'return':
                $newQuantity = $oldQuantity + $quantityChange;
                break;
            case 'out':
                if ($oldQuantity < $quantityChange) {
                    return redirect()->back()->with('error', 'Stok tidak mencukupi. Stok saat ini: ' . number_format((float)$oldQuantity, 2));
                }
                $newQuantity = $oldQuantity - $quantityChange;
                break;
            default:
                return redirect()->back()->with('error', 'Jenis stok tidak valid.');
        }

        // No longer updating average_cost as we're using product-level pricing (production_cost)
        // If unit_cost is provided, we could potentially update the product's production_cost if needed
        // But for now, we'll keep the product-level pricing separate from stock operations

        // Update stock quantity
        $branchStock->quantity = $newQuantity;
        $branchStock->last_updated = now();
        $branchStock->save();

        $stockTypeText = [
            'in' => 'Stok Masuk',
            'out' => 'Stok Keluar',
            'return' => 'Stok Return'
        ][$validated['stock_type']];

        $message = $stockTypeText . ' berhasil diproses. Stok ' . $product->name . ' di ' . $branch->name . ' sekarang: ' . number_format($newQuantity, 2);

        return redirect()->back()->with('success', $message);
    }

    public function show(SemiFinishedProduct $semiFinishedProduct)
    {
        $selectedBranchId = request('branch_id');
        $selectedBranch = null;
        $branchForStock = null;
        $showBranchSelector = true;

        // First, explicitly load the unit relationship to ensure it's available
        $semiFinishedProduct->load('unit');

        // Check if unit exists and load it if not
        if (!$semiFinishedProduct->unit && $semiFinishedProduct->unit_id) {
            $unit = Unit::find($semiFinishedProduct->unit_id);
            if ($unit) {
                $semiFinishedProduct->setRelation('unit', $unit);
            }
        }

        // Determine which branch to use for stock calculation
        if ($selectedBranchId) {
            $selectedBranch = Branch::find($selectedBranchId);
            $branchForStock = $selectedBranch;
        } elseif (auth()->check() && auth()->user()->branch) {
            $branchForStock = auth()->user()->branch;
        }

        // Load branch stocks, unit, and category relation
        if ($branchForStock) {
            $semiFinishedProduct->load([
                'semiFinishedBranchStocks' => function ($q) use ($branchForStock) {
                    $q->where('branch_id', $branchForStock->id)->with('branch');
                },
                'category',
            ]);

            // Initialize stock if it doesn't exist
            if ($semiFinishedProduct->semiFinishedBranchStocks->isEmpty()) {
                $semiFinishedProduct->initializeStockForBranch($branchForStock->id);
                // Re-load the relation after initialization
                $semiFinishedProduct->load([
                    'semiFinishedBranchStocks' => function ($q) use ($branchForStock) {
                        $q->where('branch_id', $branchForStock->id)->with('branch');
                    },
                ]);
            }

            $displayStockQuantity = $semiFinishedProduct->semiFinishedBranchStocks->first()->quantity ?? 0;
        } else {
            $semiFinishedProduct->load(['semiFinishedBranchStocks.branch']);
            $displayStockQuantity = $semiFinishedProduct->semiFinishedBranchStocks->sum('quantity');
        }

        // Get the minimum stock value from the product
        $displayMinimumStock = $semiFinishedProduct->minimum_stock ?? 0;

        return view('semi-finished-products.show', [
            'semiFinishedProduct' => $semiFinishedProduct,
            'selectedBranch' => $selectedBranch,
            'branchForStock' => $branchForStock,
            'displayStockQuantity' => $displayStockQuantity,
            'displayMinimumStock' => $displayMinimumStock,
            'showBranchSelector' => $showBranchSelector
        ]);
    }

    public function edit(SemiFinishedProduct $semiFinishedProduct)
    {
        $selectedBranchId = request('branch_id') ?? session('branch_id');
        $selectedBranch = null;
        $branchForStock = null;
        $showBranchSelector = true;

        // Determine which branch to use for stock calculation
        if ($selectedBranchId) {
            $selectedBranch = Branch::find($selectedBranchId);
            $branchForStock = $selectedBranch;
        } elseif (auth()->check() && auth()->user()->branch) {
            $branchForStock = auth()->user()->branch;
        }

        // Load branch stocks and unit relation
        if ($branchForStock) {
            $semiFinishedProduct->load([
                'semiFinishedBranchStocks' => function ($q) use ($branchForStock) {
                    $q->where('branch_id', $branchForStock->id)->with('branch');
                },
                'unit'
            ]);

            // Initialize stock if it doesn't exist
            if ($semiFinishedProduct->semiFinishedBranchStocks->isEmpty()) {
                $semiFinishedProduct->initializeStockForBranch($branchForStock->id);
                // Re-load the relation after initialization
                $semiFinishedProduct->load([
                    'semiFinishedBranchStocks' => function ($q) use ($branchForStock) {
                        $q->where('branch_id', $branchForStock->id)->with('branch');
                    },
                    'unit'
                ]);
            }

            $displayStockQuantity = $semiFinishedProduct->semiFinishedBranchStocks->first()->quantity ?? 0;
        } else {
            $semiFinishedProduct->load(['semiFinishedBranchStocks.branch', 'unit']);
            $displayStockQuantity = $semiFinishedProduct->semiFinishedBranchStocks->sum('quantity');
        }

        // Get all units for the dropdown
        $units = Unit::active()->orderBy('unit_name')->get();

        // Get categories filtered by material type for semi-finished products
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        // Calculate minimum stock value for the form (always use product-level)
        $minStockValue = $semiFinishedProduct->minimum_stock ?? 0;

        return view('semi-finished-products.edit', [
            'semiFinishedProduct' => $semiFinishedProduct,
            'selectedBranch' => $selectedBranch,
            'branchForStock' => $branchForStock,
            'displayStockQuantity' => $displayStockQuantity,
            'showBranchSelector' => $showBranchSelector,
            'units' => $units,
            'minStockValue' => $minStockValue,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, SemiFinishedProduct $semiFinishedProduct)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'production_cost' => 'nullable|numeric|min:0',
            'minimum_stock' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama bahan setengah jadi wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
            'unit_id.required' => 'Satuan wajib dipilih.',
            'unit_id.exists' => 'Satuan tidak valid.',
            'production_cost.numeric' => 'Biaya produksi harus berupa angka.',
            'production_cost.min' => 'Biaya produksi minimal 0.',
            'minimum_stock.numeric' => 'Stok minimum harus berupa angka.',
            'minimum_stock.min' => 'Stok minimum minimal 0.',
            'stock_quantity.numeric' => 'Stok harus berupa angka.',
            'stock_quantity.min' => 'Stok minimal 0.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($semiFinishedProduct->image && file_exists(public_path($semiFinishedProduct->image))) {
                unlink(public_path($semiFinishedProduct->image));
            }

            // Handle image upload
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/semi-finished'), $filename);
            $validated['image'] = 'storage/semi-finished/' . $filename;
        }

        // Update the product details
        $semiFinishedProduct->update($validated);

        // Handle stock quantity update if provided and branch is selected
        if (isset($validated['stock_quantity'])) {
            $branchId = $request->input('branch_id') ?? session('branch_id');

            // Only update stock if a specific branch is selected
            if ($branchId) {
                // Get or create branch stock record
                $branchStock = $semiFinishedProduct->semiFinishedBranchStocks()
                    ->where('branch_id', $branchId)
                    ->first();

                if ($branchStock) {
                    // Update existing stock record (quantity only)
                    $branchStock->quantity = $validated['stock_quantity'];
                    $branchStock->save();
                } else {
                    // Create new stock record if it doesn't exist
                    $semiFinishedProduct->icccnitializeStockForBranch(
                        $branchId,
                        $validated['stock_quantity'] ?? 0
                    );
                }
            }
        }

        // Get branch_id from request or session
        $branchId = request('branch_id') ?: session('branch_id');
        return redirect()->route('semi-finished-products.index', $branchId ? ['branch_id' => $branchId] : [])
            ->with('success', 'Bahan setengah jadi berhasil diperbarui.');
    }

    public function destroy(SemiFinishedProduct $semiFinishedProduct)
    {
        // Delete image file if exists
        if ($semiFinishedProduct->image && file_exists(public_path($semiFinishedProduct->image))) {
            unlink(public_path($semiFinishedProduct->image));
        }

        $semiFinishedProduct->delete();

        // Get branch_id from request or session
        $branchId = request('branch_id') ?: session('selected_branch_id');
        return redirect()->route('semi-finished-products.index', $branchId ? ['branch_id' => $branchId] : [])
            ->with('success', 'Bahan setengah jadi berhasil dihapus.');
    }

    /**
     * API endpoint to get semi-finished products list
     */
    public function apiIndex()
    {
        $semiFinishedProducts = SemiFinishedProduct::with(['unit:id,unit_name,abbreviation'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'unit_id']);

        $result = $semiFinishedProducts->map(function ($p) {
            // Use relation explicitly to avoid conflict with getUnitAttribute() accessor
            $unitRel = $p->relationLoaded('unit') ? $p->getRelation('unit') : null;
            return [
                'id' => $p->id,
                'name' => $p->name,
                'unit' => $unitRel ? [
                    'id' => $unitRel->id,
                    'unit_name' => $unitRel->unit_name,
                    'abbreviation' => $unitRel->abbreviation,
                ] : null,
            ];
        });

        return response()->json($result);
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(SemiFinishedProduct $semiFinishedProduct)
    {
        $semiFinishedProduct->update(['is_active' => !$semiFinishedProduct->is_active]);
        $status = $semiFinishedProduct->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('semi-finished-products.index')
            ->with('success', "Bahan setengah jadi berhasil {$status}.");
    }
}
