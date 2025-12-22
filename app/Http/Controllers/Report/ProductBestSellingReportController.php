<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class ProductBestSellingReportController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = SaleItem::with(['sale'])
            ->where('item_type', 'product');

        $columns = [
            ['key' => 'item_name', 'label' => 'Nama Produk'],
            ['key' => 'quantity', 'label' => 'Total Terjual'],
            ['key' => 'subtotal', 'label' => 'Total Pendapatan'],
        ];

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('item_name', 'like', $searchTerm);
        }

        // Filter by date range
        if ($request->filled('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date') && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $bestSelling */
        $bestSelling = $query->groupBy('item_id', 'item_name')
            ->selectRaw('item_id, item_name, SUM(quantity) as quantity, SUM(subtotal) as subtotal')
            ->orderBy('quantity', 'desc')
            ->paginate(10);

        $selects = [];

        // === RESPONSE ===
        if ($request->ajax()) {
            return response()->json([
                'data' => $bestSelling->items(),
                'links' => (string) $bestSelling->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('reports.product-best-selling.index', [
            'bestSelling' => $bestSelling->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $bestSelling,
        ]);
    }

    public function print(Request $request)
    {
        $query = SaleItem::with(['sale'])
            ->where('item_type', 'product');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where('item_name', 'like', $searchTerm);
        }

        // Filter by date range
        if ($request->filled('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date') && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // === AGGREGATION FOR BEST SELLING ===
        $bestSelling = $query->groupBy('item_id', 'item_name')
            ->selectRaw('item_id, item_name, SUM(quantity) as quantity, SUM(subtotal) as subtotal')
            ->orderBy('quantity', 'desc')
            ->get();

        return view('reports.product-best-selling.print', compact('bestSelling'));
    }
}
