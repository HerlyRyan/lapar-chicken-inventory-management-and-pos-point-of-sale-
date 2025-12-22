<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\SemiFinishedProduct;
use Illuminate\Http\Request;

class SemiFinishedReportController extends Controller
{
    public function index(Request $request)
    {
        $selectedBranchId = request('branch_id');
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
            $query = $query->with(['semiFinishedBranchStocks' => function($q) use ($branchForStock) {
                $q->where('branch_id', $branchForStock->id)->with('branch');
            }]);
        } else {
            // Load all semi-finished branch stocks if no specific branch
            $query = $query->with(['semiFinishedBranchStocks' => function($q) {
                $q->with('branch');
            }]);
        }

        // Initialize branch stock for products that don't have it and calculate display stock
        foreach ($semiFinishedProducts as $product) {
            if ($branchForStock) {
                // Initialize stock for specific branch if it doesn't exist
                if ($product->semiFinishedBranchStocks->isEmpty()) {
                    $product->initializeStockForBranch($branchForStock->id);
                    $product->loadMissing(['semiFinishedBranchStocks' => function($q) use ($branchForStock) {
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

        return view('reports.semi-finished.index', [
            'semiFinishedProducts' => $semiFinishedProducts->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $semiFinishedProducts,
            'selectedBranch' => $selectedBranch,
            'branchForStock' => $branchForStock,
            'showBranchSelector' => $showBranchSelector,
        ]);
    }

    public function print(Request $request)
    {
        $selectedBranchId = request('branch_id');
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

        $query = SemiFinishedProduct::with(['unit', 'category']);        

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
        
        $semiFinishedProducts = $query->get();

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

        // Load semi-finished branch stocks for the specific branch if selected
        if ($branchForStock) {
            $query = $query->with(['semiFinishedBranchStocks' => function($q) use ($branchForStock) {
                $q->where('branch_id', $branchForStock->id)->with('branch');
            }]);
        } else {
            // Load all semi-finished branch stocks if no specific branch
            $query = $query->with(['semiFinishedBranchStocks' => function($q) {
                $q->with('branch');
            }]);
        }

        // Initialize branch stock for products that don't have it and calculate display stock
        foreach ($semiFinishedProducts as $product) {
            if ($branchForStock) {
                // Initialize stock for specific branch if it doesn't exist
                if ($product->semiFinishedBranchStocks->isEmpty()) {
                    $product->initializeStockForBranch($branchForStock->id);
                    $product->loadMissing(['semiFinishedBranchStocks' => function($q) use ($branchForStock) {
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

        return view('reports.semi-finished.print', [
            'semiFinishedProducts' => $semiFinishedProducts,
            'pagination' => $semiFinishedProducts,
            'selectedBranch' => $selectedBranch,
            'branchForStock' => $branchForStock,
            'showBranchSelector' => $showBranchSelector,
        ]);
    }
}
