<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\StockTransfer;
use Illuminate\Http\Request;

class StockTransferReportController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = StockTransfer::with(['fromBranch', 'toBranch', 'sentByUser', 'handledByUser', 'finishedProduct', 'semiFinishedProduct']);

        $columns = [            
            ['key' => 'item_type', 'label' => 'Tipe Item'],
            ['key' => 'item_id', 'label' => 'Item'],
            ['key' => 'from_branch_id', 'label' => 'Dari Cabang'],
            ['key' => 'to_branch_id', 'label' => 'Ke Cabang'],
            ['key' => 'quantity', 'label' => 'Jumlah'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'notes', 'label' => 'Catatan'],
            ['key' => 'created_at', 'label' => 'Tanggal Pengiriman'],
            ['key' => 'handled_at', 'label' => 'Tanggal Penanganan'],
        ];

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('notes', 'like', $searchTerm)
                    ->orWhere('response_notes', 'like', $searchTerm);
            });
        }

        // Filter by from_branch
        if ($request->filled('from_branch_id') && !empty($request->from_branch_id)) {
            $query->where('from_branch_id', $request->from_branch_id);
        }

        // Filter by to_branch
        if ($request->filled('to_branch_id') && !empty($request->to_branch_id)) {
            $query->where('to_branch_id', $request->to_branch_id);
        }

        // Filter by item_type
        if ($request->filled('item_type') && $request->item_type != 'all') {
            $query->where('item_type', $request->item_type);
        }

        // Filter by status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
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

            switch ($sortBy) {
                case 'from_branch_id':
                    $query->leftJoin('branches as fb', 'fb.id', '=', 'stock_transfers.from_branch_id')
                        ->orderBy('fb.name', $sortDir)
                        ->select('stock_transfers.*');
                    break;

                case 'to_branch_id':
                    $query->leftJoin('branches as tb', 'tb.id', '=', 'stock_transfers.to_branch_id')
                        ->orderBy('tb.name', $sortDir)
                        ->select('stock_transfers.*');
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $transfers */
        $transfers = $query->paginate(10);

        // Get all branches for filter dropdown
        $branches = Branch::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();

        $itemTypes = [
            'finished' => 'Finished Product',
            'semi-finished' => 'Semi-Finished Product',
        ];

        $statuses = [
            'sent' => 'Dikirim',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
        ];

        $selects = [
            ['name' => 'from_branch_id', 'label' => 'Dari Cabang', 'options' => $branches],
            ['name' => 'to_branch_id', 'label' => 'Ke Cabang', 'options' => $branches],
            ['name' => 'item_type', 'label' => 'Semua Tipe Item', 'options' => $itemTypes],
            ['name' => 'status', 'label' => 'Semua Status', 'options' => $statuses],
        ];

        // === RESPONSE ===
        if ($request->ajax()) {
            return response()->json([
                'data' => $transfers->items(),
                'links' => (string) $transfers->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('reports.stock-transfers.index', [
            'transfers' => $transfers->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $transfers,
        ]);
    }

    public function print(Request $request)
    {
        $query = StockTransfer::with(['fromBranch', 'toBranch', 'sentByUser', 'handledByUser', 'finishedProduct', 'semiFinishedProduct']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('notes', 'like', $searchTerm)
                    ->orWhere('response_notes', 'like', $searchTerm);
            });
        }

        // Filter by from_branch
        if ($request->filled('from_branch_id') && !empty($request->from_branch_id)) {
            $query->where('from_branch_id', $request->from_branch_id);
        }

        // Filter by to_branch
        if ($request->filled('to_branch_id') && !empty($request->to_branch_id)) {
            $query->where('to_branch_id', $request->to_branch_id);
        }

        // Filter by item_type
        if ($request->filled('item_type') && $request->item_type != 'all') {
            $query->where('item_type', $request->item_type);
        }

        // Filter by status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
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

            switch ($sortBy) {
                case 'from_branch_id':
                    $query->leftJoin('branches as fb', 'fb.id', '=', 'stock_transfers.from_branch_id')
                        ->orderBy('fb.name', $sortDir)
                        ->select('stock_transfers.*');
                    break;

                case 'to_branch_id':
                    $query->leftJoin('branches as tb', 'tb.id', '=', 'stock_transfers.to_branch_id')
                        ->orderBy('tb.name', $sortDir)
                        ->select('stock_transfers.*');
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $transfers = $query->get();

        return view('reports.stock-transfers.print', compact('transfers'));
    }
}
