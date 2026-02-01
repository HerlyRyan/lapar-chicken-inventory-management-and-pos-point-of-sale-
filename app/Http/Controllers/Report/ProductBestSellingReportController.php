<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class ProductBestSellingReportController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = SaleItem::query()
            ->where('sale_items.item_type', 'product')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('branches', 'branches.id', '=', 'sales.branch_id')
            ->join('finished_products', 'finished_products.id', '=', 'sale_items.item_id')
            ->join('categories', 'categories.id', '=', 'finished_products.category_id');

        // Search
        if ($request->filled('search')) {
            $query->where('sale_items.item_name', 'like', "%{$request->search}%");
        }

        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('sale_items.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('sale_items.created_at', '<=', $request->end_date);
        }

        // Branch Filter
        if ($request->filled('branch_id')) {
            $query->where('sales.branch_id', $request->branch_id);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('finished_products.category_id', $request->category_id);
        }

        // aggregation
        /** @var \Illuminate\Pagination\LengthAwarePaginator $bestSelling */
        $bestSelling = $query
            ->groupBy([
                'sales.branch_id',
                'branches.name',
                'sale_items.item_id',
                'sale_items.item_name',
                'finished_products.category_id',
                'categories.name',
            ])
            ->selectRaw('
            sales.branch_id,
            branches.name AS branch_name,
            sale_items.item_id,
            sale_items.item_name,
            finished_products.category_id,
            categories.name AS category_name,
            SUM(sale_items.quantity) AS quantity,
            SUM(sale_items.subtotal) AS subtotal
        ')
            ->orderByDesc('quantity')
            ->paginate(10);

        // master data select
        $branches = Branch::query()
            ->where('is_active', true)
            ->where('type', '!=', 'production')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $columns = [
            ['key' => 'branch_name', 'label' => 'Cabang'],
            ['key' => 'item_name', 'label' => 'Nama Produk'],
            ['key' => 'category_name', 'label' => 'Kategori'],
            ['key' => 'quantity', 'label' => 'Total Terjual'],
            ['key' => 'subtotal', 'label' => 'Total Pendapatan'],
        ];

        $selects = [
            ['name' => 'branch_id', 'label' => 'Semua Cabang', 'options' => $branches],
            ['name' => 'category_id', 'label' => 'Semua Kategori', 'options' => $categories],
        ];

        // response API
        if ($request->ajax()) {
            return response()->json([
                'data'  => $bestSelling->items(),
                'links' => (string) $bestSelling->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('reports.product-best-selling.index', [
            'bestSelling' => $bestSelling->items(),
            'columns'     => $columns,
            'selects'     => $selects,
            'pagination'  => $bestSelling,
        ]);
    }

    public function print(Request $request)
    {
        $query = SaleItem::query()
            ->where('sale_items.item_type', 'product')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('branches', 'branches.id', '=', 'sales.branch_id')
            ->join('finished_products', 'finished_products.id', '=', 'sale_items.item_id')
            ->join('categories', 'categories.id', '=', 'finished_products.category_id');

        // Search
        if ($request->filled('search')) {
            $query->where('sale_items.item_name', 'like', "%{$request->search}%");
        }

        // Date filter
        if ($request->filled('start_date')) {
            $query->whereDate('sale_items.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('sale_items.created_at', '<=', $request->end_date);
        }

        // Branch Filter
        if ($request->filled('branch_id')) {
            $query->where('sales.branch_id', $request->branch_id);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('finished_products.category_id', $request->category_id);
        }

        // === AGGREGATION FOR BEST SELLING ===
        $bestSelling = $query
            ->groupBy([
                'sales.branch_id',
                'branches.name',
                'sale_items.item_id',
                'sale_items.item_name',
                'finished_products.category_id',
                'categories.name',
            ])
            ->selectRaw('
            sales.branch_id,
            branches.name AS branch_name,
            sale_items.item_id,
            sale_items.item_name,
            finished_products.category_id,
            categories.name AS category_name,
            SUM(sale_items.quantity) AS quantity,
            SUM(sale_items.subtotal) AS subtotal
        ')
            ->orderByDesc('quantity')
            ->get();

        return view('reports.product-best-selling.print', compact('bestSelling'));
    }
}
