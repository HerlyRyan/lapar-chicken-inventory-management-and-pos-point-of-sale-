<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['branch', 'user', 'items']);
        $cardQuery = Sale::query()
            ->where('status', 'completed');

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
            $query->where(function ($q) use ($searchTerm) {
                $q->where('sale_number', 'like', $searchTerm)
                    ->orWhere('customer_name', 'like', $searchTerm)
                    ->orWhere('customer_phone', 'like', $searchTerm);
            });
        }

        // Filter by branch
        if ($request->filled('branch_id') && !empty($request->branch_id)) {
            $query->where('branch_id', $request->branch_id);
            $cardQuery->where('branch_id', $request->branch_id);
        }

        // Filter by status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method') && $request->payment_method != 'all') {
            $query->where('payment_method', $request->payment_method);
            $cardQuery->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
            $cardQuery->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date') && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
            $cardQuery->whereDate('created_at', '<=', $request->end_date);
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
        $branches = Branch::where('is_active', true)->where('type', '=', 'branch')->orderBy('name')->pluck('name', 'id')->toArray();

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

        $totalRevenue = $cardQuery->sum('final_amount');

        // === RESPONSE ===
        if ($request->ajax()) {
            return response()->json([
                'data' => $sales->items(),
                'links' => (string) $sales->links('vendor.pagination.tailwind'),
                'totalRevenue' => $totalRevenue,
            ]);
        }


        return view('reports.sales.index', compact('sales', 'branches', 'columns', 'selects', 'totalRevenue'));
    }
    public function print(Request $request)
    {
        $query = Sale::with(['branch', 'user', 'items']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
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

        $sales = $query->get();

        return view('reports.sales.print', compact('sales'));
    }
}
