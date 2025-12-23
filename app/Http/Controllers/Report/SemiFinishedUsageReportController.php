<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\SemiFinishedProduct;
use App\Models\SemiFinishedUsageRequest;
use Illuminate\Http\Request;

class SemiFinishedUsageReportController extends Controller
{
    public function index(Request $request)
    {
        $query = SemiFinishedUsageRequest::query()
            ->join(
                'semi_finished_usage_request_items as items',
                'items.semi_finished_request_id',
                '=',
                'semi_finished_usage_requests.id'
            )
            ->leftJoin(
                'branches',
                'branches.id',
                '=',
                'semi_finished_usage_requests.requesting_branch_id'
            )
            ->leftJoin(
                'semi_finished_products',
                'semi_finished_products.id',
                '=',
                'items.semi_finished_product_id'
            )
            ->selectRaw('
        DATE(semi_finished_usage_requests.created_at) as usage_date,
        branches.id as branch_id,
        branches.name as branch_name,
        semi_finished_products.name as product_name,
        SUM(items.quantity) as total_quantity,
        items.semi_finished_product_id as product_id
        ')
            ->groupBy(
                'usage_date',
                'branches.id',
                'branches.name',
                'semi_finished_products.name',
                'items.semi_finished_product_id',
            );

        // === FILTER CABANG ===
        if ($request->filled('branch_id')) {
            $query->where('semi_finished_usage_requests.requesting_branch_id', $request->branch_id);
        }

        // === FILTER SEMI FINISHED PRODUCT ===
        if ($request->filled('product_id')) {
            $query->where('items.semi_finished_product_id', $request->product_id);
        }

        // === FILTER TANGGAL ===
        if ($request->filled('start_date')) {
            $query->whereDate('semi_finished_usage_requests.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('semi_finished_usage_requests.created_at', '<=', $request->end_date);
        }

        // === SORTING ===
        if ($sortBy = $request->get('sort_by')) {
            $sortDir = $request->get('sort_dir', 'desc');
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('usage_date', 'asc');
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $usages */
        $usages = $query->paginate(10);

        // === COLUMNS ===
        $columns = [
            ['key' => 'usage_date', 'label' => 'Tanggal'],
            ['key' => 'branch_id', 'label' => 'Cabang'],
            ['key' => 'product_id', 'label' => 'Bahan Setengah Jadi'],
            ['key' => 'total_quantity', 'label' => 'Total Digunakan'],
        ];

        // === FILTER SELECT ===
        $branches = Branch::where('type', '=', 'branch')->orderBy('name')->pluck('name', 'id')->toArray();
        $semiFinishedProduct = SemiFinishedProduct::orderBy('name')->pluck('name', 'id')->toArray();

        $selects = [
            ['name' => 'branch_id', 'label' => 'Cabang', 'options' => $branches],
            ['name' => 'product_id', 'label' => 'Bahan Setengah Jadi', 'options' => $semiFinishedProduct],
        ];

        // === RESPONSE AJAX ===
        if ($request->ajax()) {
            return response()->json([
                'data' => $usages->items(),
                'links' => (string) $usages->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('reports.semi-finished-usage.index', [
            'usages' => $usages->items(),
            'columns' => $columns,
            'selects' => $selects,
            'pagination' => $usages,
        ]);
    }
    public function print(Request $request)
    {
        $query = SemiFinishedUsageRequest::query()
            ->join(
                'semi_finished_usage_request_items as items',
                'items.semi_finished_request_id',
                '=',
                'semi_finished_usage_requests.id'
            )
            ->leftJoin(
                'branches',
                'branches.id',
                '=',
                'semi_finished_usage_requests.requesting_branch_id'
            )
            ->leftJoin(
                'semi_finished_products',
                'semi_finished_products.id',
                '=',
                'items.semi_finished_product_id'
            )
            ->selectRaw('
        DATE(semi_finished_usage_requests.created_at) as usage_date,
        branches.id as branch_id,
        branches.name as branch_name,
        semi_finished_products.name as product_name,
        SUM(items.quantity) as total_quantity,
        items.semi_finished_product_id as product_id
        ')
            ->groupBy(
                'usage_date',
                'branches.id',
                'branches.name',
                'semi_finished_products.name',
                'items.semi_finished_product_id',
            );

        // FILTER CABANG
        if ($request->filled('branch_id')) {
            $query->where('semi_finished_usage_requests.requesting_branch_id', $request->branch_id);
        }

        // === FILTER SEMI FINISHED PRODUCT ===
        if ($request->filled('product_id')) {
            $query->where('items.semi_finished_product_id', $request->product_id);
        }

        // FILTER TANGGAL
        if ($request->filled('start_date')) {
            $query->whereDate('semi_finished_usage_requests.created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('semi_finished_usage_requests.created_at', '<=', $request->end_date);
        }

        $usages = $query
            ->orderBy('usage_date', 'asc')
            ->get();

        return view('reports.semi-finished-usage.print', compact('usages'));
    }
}
