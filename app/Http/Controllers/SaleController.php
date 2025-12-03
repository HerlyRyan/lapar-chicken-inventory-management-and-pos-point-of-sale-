<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Branch;
use App\Models\Product;
use App\Models\FinishedProduct;
use App\Models\FinishedBranchStock;
use App\Models\SalesPackage;
use App\Models\SalesPackageItem;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaleController extends Controller
{
    /**
     * Get products with stock for a specific branch
     */
    public function apiProducts(Request $request)
    {
        try {
            $branchId = $request->branch_id;
            
            Log::debug('getProducts called with branch_id: ' . $branchId);
            
            if (!$branchId || $branchId == '-- Pilih Cabang --') {
                return response()->json(['error' => 'Pilih cabang terlebih dahulu'], 400);
            }
            
            // First check if branch exists
            $branch = Branch::find($branchId);
            if (!$branch) {
                Log::error('Branch with ID ' . $branchId . ' does not exist');
                return response()->json(['error' => 'Cabang tidak ditemukan'], 400);
            }
            
            // Fetch all active finished products first
            Log::debug('Querying finished products...');
            
            // Get all active products regardless of stock
            $products = FinishedProduct::with(['category'])
                ->where('is_active', true)
                ->get();
            
            Log::debug('Found ' . $products->count() . ' active finished products');
            
            // Then fetch all stocks for this branch in one query to improve performance
            $branchStocks = FinishedBranchStock::where('branch_id', $branchId)
                ->get()
                ->keyBy('finished_product_id');
            
            Log::debug('Found ' . $branchStocks->count() . ' stock records for branch ID ' . $branchId);
            
            $result = [];
            
            foreach ($products as $product) {
                try {
                    // Skip products without category
                    if (!$product->category) {
                        Log::warning('Product #' . $product->id . ' (' . $product->name . ') has no category');
                        continue;
                    }
                    
                    // Get stock for this product from the pre-fetched stocks
                    $stockRecord = $branchStocks->get($product->id);
                    $stock = (int) round($stockRecord ? $stockRecord->quantity : 0);
                    
                    $result[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'code' => $product->code,
                        'description' => $product->description,
                        'price' => (int) round($product->price),
                        'stock' => $stock,
                        'photo' => $product->photo ? asset('storage/' . $product->photo) : asset('images/no-image.png'),
                        'category' => [
                            'id' => $product->category->id,
                            'name' => $product->category->name
                        ]
                    ];
                } catch (\Exception $innerEx) {
                    Log::error('Error processing product #' . $product->id . ': ' . $innerEx->getMessage());
                    // Continue to next product
                }
            }
            
            Log::debug('Successfully processed ' . count($result) . ' products for display');
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Error fetching products: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data produk'], 500);
        }
    }
    
    /**
     * Get packages with calculated available stock for a specific branch
     */
    public function apiPackages(Request $request)
    {
        try {
            $branchId = $request->branch_id;
            
            Log::debug('getPackages called with branch_id: ' . $branchId);
            
            if (!$branchId || $branchId == '-- Pilih Cabang --') {
                return response()->json(['error' => 'Pilih cabang terlebih dahulu'], 400);
            }
            
            // First check if branch exists
            $branchExists = Branch::where('id', $branchId)->exists();
            if (!$branchExists) {
                Log::error('Branch with ID ' . $branchId . ' does not exist');
                return response()->json(['error' => 'Cabang tidak ditemukan'], 400);
            }
            
            Log::debug('Querying sales packages...');
            
            // Get all active packages with their components and categories
            $packages = SalesPackage::with(['packageItems.finishedProduct', 'category'])
                ->where('is_active', true)
                ->whereNotNull('category_id')
                ->get();
            
            Log::debug('Found ' . $packages->count() . ' active sales packages');
            
            // Get all finished product IDs from package items
            $finishedProductIds = [];
            foreach ($packages as $package) {
                if ($package->packageItems->isNotEmpty()) {
                    foreach ($package->packageItems as $item) {
                        if ($item->finished_product_id) {
                            $finishedProductIds[] = $item->finished_product_id;
                        }
                    }
                }
            }
            
            // Fetch all relevant stock records in one query for performance
            $branchStocks = FinishedBranchStock::where('branch_id', $branchId)
                ->whereIn('finished_product_id', $finishedProductIds)
                ->get()
                ->keyBy('finished_product_id');
            
            Log::debug('Found ' . $branchStocks->count() . ' stock records for branch ID ' . $branchId);
            
            $result = [];
            
            foreach ($packages as $package) {
                try {
                    // Skip packages without category_id (relationship may be null due to data/state)
                    if (empty($package->category_id)) {
                        Log::warning('Package #' . $package->id . ' (' . $package->name . ') has no category_id');
                        continue;
                    }
                    
                    // Skip empty packages
                    if ($package->packageItems->isEmpty()) {
                        Log::warning('Package #' . $package->id . ' (' . $package->name . ') has no items');
                        continue;
                    }
                    
                    // Calculate available stock based on component products
                    $minAvailableStock = null;
                    $components = [];
                    $hasInvalidComponent = false;
                    
                    foreach ($package->packageItems as $item) {
                        try {
                            // Skip items without product
                            if (!$item->finishedProduct) {
                                Log::warning('Package item #' . $item->id . ' has no finished product');
                                $hasInvalidComponent = true;
                                continue;
                            }
                            
                            // Get stock from pre-fetched stocks
                            $stockRecord = $branchStocks->get($item->finished_product_id);
                            $stockQuantity = $stockRecord ? $stockRecord->quantity : 0;
                            
                            // Calculate how many packages can be made with this component
                            $availableStock = $stockQuantity > 0 ? floor($stockQuantity / $item->quantity) : 0;
                            
                            if ($minAvailableStock === null || $availableStock < $minAvailableStock) {
                                $minAvailableStock = $availableStock;
                            }
                            
                            $components[] = [
                                'id' => $item->finishedProduct->id,
                                'name' => $item->finishedProduct->name,
                                'quantity' => $item->quantity,
                                'available' => $stockQuantity
                            ];
                        } catch (\Exception $componentEx) {
                            Log::error('Error processing package component: ' . $componentEx->getMessage());
                            $hasInvalidComponent = true;
                        }
                    }
                    
                    // Skip packages with invalid components
                    if ($hasInvalidComponent) {
                        Log::warning('Package #' . $package->id . ' (' . $package->name . ') has invalid components');
                        continue;
                    }
                    
                    // Do not skip packages with no stock; frontend will display with disabled action
                    $minAvailableStock = (int) max(0, (int) ($minAvailableStock ?? 0));
                    
                    $result[] = [
                        'id' => $package->id,
                        'name' => $package->name,
                        'code' => $package->code,
                        'description' => $package->description,
                        'category_id' => $package->category_id,
                        'price' => (int) round($package->final_price),
                        // Provide both for compatibility; frontend uses calculated_stock
                        'calculated_stock' => $minAvailableStock,
                        'stock' => $minAvailableStock,
                        'photo' => $package->image ? asset('storage/' . $package->image) : asset('images/no-image.png'),
                        'category' => [
                            'id' => $package->category_id,
                            'name' => optional($package->category)->name
                        ],
                        'components' => $components
                    ];
                } catch (\Exception $packageEx) {
                    Log::error('Error processing package #' . $package->id . ': ' . $packageEx->getMessage());
                }
            }
            
            Log::debug('Successfully processed ' . count($result) . ' packages for display');
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Error fetching packages: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Terjadi kesalahan saat memuat data paket'], 500);
        }
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['branch', 'user', 'items']);

        $columns = [
            ['key' => 'sale_number', 'label' => 'No. Transaksi'],
            ['key' => 'created_at', 'label' => 'Tanggal'],
            ['key' => 'branch_id', 'label' => 'Cabang'],
            ['key' => 'customer', 'label' => 'Pelanggan'],
            ['key' => 'final_amount', 'label' => 'Total'],
            ['key' => 'payment_method', 'label' => 'Pembayaran'],
            ['key' => 'status', 'label' => 'Status'],
        ];
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('sale_number', 'like', $searchTerm)
                  ->orWhere('customer_name', 'like', $searchTerm)
                  ->orWhere('customer_phone', 'like', $searchTerm);
            });
        }
        
        // Filter by branch
        if ($request->filled('branch_id') && !empty($request->branch_id)) {
            $query->where('branch_id', $request->branch_id);
        }
        
        // Filter by status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by payment method
        if ($request->filled('payment_method') && $request->payment_method != 'all') {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Filter by date range
        if ($request->filled('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date') && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // === SORTING ===
        if ($sortBy = $request->get('sort_by')) {
            $sortDir = $request->get('sort_dir', 'asc');

            // 🧩 Deteksi kolom relasi
            switch ($sortBy) {
                case 'requested_by':
                    $query->leftjoin('users', 'users.id', '=', 'production_requests.requested_by')
                        ->orderBy('users.name', $sortDir)
                        ->select('production_requests.*');
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $sales */
        $sales = $query->paginate(10);

        // Get all branches for filter dropdown
        $branches = Branch::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();

        $statuses = [
            'pending' => 'Pending',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $paymentMethod = [
            'cash' => 'Tunai',
            'qris' => 'QRIS',
        ];

        $selects = [
            ['name' => 'branch_id', 'label' => 'Semua Cabang', 'options' => $branches],
            [
                'name' => 'status',
                'label' => 'Semua Status',
                'options' => $statuses,
            ],
            [
                'name' => 'payment_method',
                'label' => 'Semua Metode Pembayaran',
                'options' => $paymentMethod,
            ],

        ];

        // === RESPONSE ===
        if ($request->ajax()) {
            return response()->json([
                'data' => $sales->items(),
                'links' => (string) $sales->links('vendor.pagination.tailwind'),
            ]);
        }
        
        return view('sales.index', compact('sales', 'branches', 'columns', 'selects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get active retail branches only (exclude production centers)
        $branches = Branch::active()->retail()->get();
        
        return view('sales.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            // Client totals will be ignored; keep nullable for compatibility
            'subtotal_amount' => 'nullable|integer|min:0',
            'discount_type' => 'required|in:none,percentage,nominal',
            // percentage may be decimal; nominal will be coerced to integer when computing
            'discount_value' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|integer|min:0',
            'final_amount' => 'nullable|integer|min:0',
            'payment_method' => 'required|in:cash,qris',
            'paid_amount' => 'required_if:payment_method,cash|integer|min:0',
            'change_amount' => 'nullable|integer|min:0',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|in:product,package',
            'items.*.item_id' => 'required|integer',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            // Client subtotal will be ignored; keep nullable for compatibility
            'items.*.subtotal' => 'nullable|integer|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Generate sale number
            $saleNumber = 'TRX-' . date('Ymd') . '-' . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Generate sale code (contoh: SL2508085130)
            $saleCode = 'SL' . date('ymd') . random_int(1000, 9999);

            // Recompute totals server-side using integer math
            $items = collect($request->items)->map(function ($item) {
                $qty = (int) $item['quantity'];
                $unit = (int) $item['unit_price'];
                $sub = (int) ($qty * $unit);
                return [
                    'item_type' => $item['item_type'],
                    'item_id' => (int) $item['item_id'],
                    'item_name' => $item['item_name'],
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'subtotal' => $sub,
                ];
            });

            $computedSubtotal = (int) $items->sum('subtotal');
            $discountType = $request->discount_type;
            $discountValueRaw = $request->discount_value ?? 0;
            $discountValue = is_null($discountValueRaw) ? 0 : (float) $discountValueRaw; // percentage may be decimal

            if ($discountType === 'percentage') {
                $computedDiscount = (int) round(($computedSubtotal * $discountValue) / 100);
            } elseif ($discountType === 'nominal') {
                $computedDiscount = (int) round($discountValue);
            } else { // none
                $computedDiscount = 0;
            }

            if ($computedDiscount < 0) { $computedDiscount = 0; }
            if ($computedDiscount > $computedSubtotal) { $computedDiscount = $computedSubtotal; }

            $computedFinal = (int) max(0, $computedSubtotal - $computedDiscount);

            // Determine payment amounts
            $paymentMethod = $request->payment_method;
            if ($paymentMethod === 'cash') {
                $paidAmount = (int) ($request->paid_amount ?? 0);
                $changeAmount = (int) max(0, $paidAmount - $computedFinal);
            } else { // qris
                $paidAmount = $computedFinal;
                $changeAmount = 0;
            }

            // Build required finished product quantities for this sale (DRY helper)
            $requirements = $this->computeRequirementsFromItems($items);

            // Validate and deduct branch stock atomically
            $productIds = array_keys($requirements);
            if (!empty($productIds)) {
                // Lock relevant stock rows for update
                $stocks = FinishedBranchStock::where('branch_id', $request->branch_id)
                    ->whereIn('finished_product_id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('finished_product_id');

                $productNames = FinishedProduct::whereIn('id', $productIds)->get()->keyBy('id');

                // Validate availability
                foreach ($requirements as $pid => $reqQty) {
                    $available = (int) round(optional($stocks->get($pid))->quantity ?? 0);
                    if ($available < $reqQty) {
                        $pname = optional($productNames->get($pid))->name ?? ('Produk #' . $pid);
                        throw new \Exception("Stok tidak mencukupi untuk {$pname}. Dibutuhkan {$reqQty}, tersedia {$available}.");
                    }
                }

                // Deduct
                foreach ($requirements as $pid => $reqQty) {
                    $stockRow = $stocks->get($pid);
                    if (!$stockRow) {
                        // Should not happen due to validation above (available would be 0)
                        throw new \Exception('Data stok cabang tidak ditemukan untuk produk ID: ' . $pid);
                    }
                    $stockRow->quantity = (int) round($stockRow->quantity) - (int) $reqQty;
                    $stockRow->save();
                }
            }

            // Create sale with computed values
            $sale = Sale::create([
                'sale_code' => $saleCode,
                'sale_number' => $saleNumber,
                'branch_id' => $request->branch_id,
                'user_id' => Auth::id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'subtotal_amount' => $computedSubtotal,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_amount' => $computedDiscount,
                'final_amount' => $computedFinal,
                'payment_method' => $paymentMethod,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'status' => 'completed'
            ]);

            // Create sale items from computed values
            foreach ($items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'item_type' => $item['item_type'],
                    'item_id' => $item['item_id'],
                    'item_name' => $item['item_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            DB::commit();
            
            // Return JSON if requested via AJAX/Fetch API
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json([
                    'sale_id' => $sale->id,
                    'message' => 'Penjualan berhasil disimpan.'
                ], 200);
            }
            
            return redirect()->route('sales.show', $sale)
                ->with('success', 'Penjualan berhasil disimpan.');
                
        } catch (\Exception $e) {
            DB::rollback();
            // If this was an AJAX/JSON request, return JSON error so POS can display it
            if ($request->expectsJson() || $request->wantsJson() || $request->isJson()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load(['branch', 'user', 'items']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        // Sales should not be editable once completed
        return redirect()->route('sales.show', $sale)
            ->with('info', 'Transaksi yang sudah selesai tidak dapat diedit.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        // Sales should not be editable once completed
        return redirect()->route('sales.show', $sale)
            ->with('info', 'Transaksi yang sudah selesai tidak dapat diedit.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale)
    {
        // Prevent double cancellation
        if ($sale->status === 'cancelled') {
            return redirect()->route('sales.index')
                ->with('info', 'Transaksi sudah dibatalkan sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Load items and compute required finished products
            $sale->load('items');
            $requirements = $this->computeRequirementsFromItems($sale->items);

            // Lock existing stock rows for update
            $productIds = array_keys($requirements);
            $stocks = collect();
            if (!empty($productIds)) {
                $stocks = FinishedBranchStock::where('branch_id', $sale->branch_id)
                    ->whereIn('finished_product_id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('finished_product_id');
            }

            $notesBase = 'Pembatalan Penjualan ' . ($sale->sale_number ?? ('#' . $sale->id));

            foreach ($requirements as $pid => $qty) {
                $stockRow = $stocks->get($pid);
                if (!$stockRow) {
                    $stockRow = FinishedBranchStock::create([
                        'branch_id' => $sale->branch_id,
                        'finished_product_id' => (int) $pid,
                        'quantity' => 0,
                    ]);
                }
                $stockRow->updateStock('in', (int) $qty, $notesBase, Auth::id());
            }

            // Mark sale as cancelled
            $sale->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('sales.index')
                ->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('sales.index')
                ->with('error', 'Gagal membatalkan transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Compute required finished product quantities from sale items.
     * Accepts an array/Collection of arrays (from request) or SaleItem models.
     * Returns [finished_product_id => quantity].
     */
    private function computeRequirementsFromItems($items): array
    {
        // Normalize to iterable
        if ($items instanceof \Illuminate\Support\Collection) {
            $iter = $items;
        } else {
            $iter = collect($items);
        }

        $requirements = [];
        $packageIds = [];

        foreach ($iter as $it) {
            $type = is_array($it) ? ($it['item_type'] ?? null) : ($it->item_type ?? null);
            $id = (int) (is_array($it) ? ($it['item_id'] ?? 0) : ($it->item_id ?? 0));
            $qty = (int) (is_array($it) ? ($it['quantity'] ?? 0) : ($it->quantity ?? 0));

            if ($type === 'product') {
                $requirements[$id] = ($requirements[$id] ?? 0) + $qty;
            } elseif ($type === 'package') {
                $packageIds[] = $id;
            }
        }

        $packageIds = array_values(array_unique(array_filter($packageIds)));
        if (!empty($packageIds)) {
            $packages = SalesPackage::with(['packageItems'])
                ->whereIn('id', $packageIds)
                ->get()
                ->keyBy('id');

            foreach ($iter as $it) {
                $type = is_array($it) ? ($it['item_type'] ?? null) : ($it->item_type ?? null);
                if ($type !== 'package') continue;

                $pkgId = (int) (is_array($it) ? ($it['item_id'] ?? 0) : ($it->item_id ?? 0));
                $qty = (int) (is_array($it) ? ($it['quantity'] ?? 0) : ($it->quantity ?? 0));
                $pkg = $packages->get($pkgId);
                if (!$pkg) {
                    throw new \Exception('Paket tidak ditemukan (ID: ' . $pkgId . ').');
                }
                foreach ($pkg->packageItems as $comp) {
                    if (!$comp->finished_product_id || $comp->quantity <= 0) continue;
                    $pid = (int) $comp->finished_product_id;
                    $reqQty = (int) ($comp->quantity * $qty);
                    $requirements[$pid] = ($requirements[$pid] ?? 0) + $reqQty;
                }
            }
        }

        return $requirements;
    }
}
