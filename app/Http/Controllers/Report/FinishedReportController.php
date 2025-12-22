<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\FinishedProduct;
use Illuminate\Http\Request;

class FinishedReportController extends Controller
{
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
        if (session('branch_id')) {
            $selectedBranch = Branch::where('is_active', true)->find(session('branch_id'));
        }

        // dd($selectedBranch);

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
            ['key' => 'stock', 'label' => 'Stok Di Cabang'],
            ['key' => 'minimum_stock', 'label' => 'Stok Minimum'],
            ['key' => 'price', 'label' => 'Harga Jual'],
            ['key' => 'is_active', 'label' => 'Status'],
        ];

        // Load finished branch stocks for the specific branch if selected
        if ($branchForStock) {
            $query = $query->with(['finishedBranchStocks' => function ($q) use ($branchForStock) {
                $q->where('branch_id', $branchForStock->id)->with('branch');
            }]);
        } else {
            // Load all finished branch stocks if no specific branch
            $query = $query->with(['finishedBranchStocks' => function ($q) {
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
                    $product->load(['finishedBranchStocks' => function ($q) use ($branchForStock) {
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

        return view('reports.finished.index', [
            'finishedProducts' => $finishedProducts->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $finishedProducts,
            'selectedBranch' => $selectedBranch,
            'branchForStock' => $branchForStock,
            'showBranchSelector' => $showBranchSelector,
        ]);
    }

    public function print(Request $request)
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
        if (session('branch_id')) {
            $selectedBranch = Branch::where('is_active', true)->find(session('branch_id'));
        }

        // dd($selectedBranch);

        // Determine which branch to use for stock filtering
        $branchForStock = $selectedBranch ?? $currentBranch;
        $showBranchSelector = $canSwitchBranch || !$currentBranch;

        // Build the query for finished products
        $query = FinishedProduct::with(['category', 'unit']);       

        // Load finished branch stocks for the specific branch if selected
        if ($branchForStock) {
            $query = $query->with(['finishedBranchStocks' => function ($q) use ($branchForStock) {
                $q->where('branch_id', $branchForStock->id)->with('branch');
            }]);
        } else {
            // Load all finished branch stocks if no specific branch
            $query = $query->with(['finishedBranchStocks' => function ($q) {
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
        
        $finishedProducts = $query->get();

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

        // Initialize branch stock for products that don't have it and calculate display stock
        foreach ($finishedProducts as $product) {
            if ($branchForStock) {
                // Initialize stock for specific branch if it doesn't exist
                if ($product->finishedBranchStocks->isEmpty()) {
                    $product->initializeStockForBranch($branchForStock->id);
                    $product->load(['finishedBranchStocks' => function ($q) use ($branchForStock) {
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

        return view('reports.finished.print', [
            'finishedProducts' => $finishedProducts,          
            'selectedBranch' => $selectedBranch,
            'branchForStock' => $branchForStock,
            'showBranchSelector' => $showBranchSelector,
        ]);
    }
}
