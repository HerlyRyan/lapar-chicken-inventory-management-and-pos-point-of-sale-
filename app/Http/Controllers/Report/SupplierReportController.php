<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierReportController extends Controller
{
    public function index(Request $request)
    {

        // Base query
        $query = Supplier::query();

        // for column selection
        $columns = [
            ['key' => 'code', 'label' => 'Kode'],
            ['key' => 'name', 'label' => 'Nama'],
            ['key' => 'address', 'label' => 'Alamat'],
            ['key' => 'phone', 'label' => 'No Telepon'],
            ['key' => 'is_active', 'label' => 'Status'],
        ];

        // Search global
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status = $request->get('is_active')) {
            $query->where('is_active', $status);
        }

        // Sorting
        if ($sortBy = $request->get('sort_by')) {
            $sortDir = $request->get('sort_dir', 'asc');
            $query->orderBy($sortBy, $sortDir);
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $suppliers */
        $suppliers = $query->paginate(10);

        $statuses = [
            1 => 'Aktif',
            0 => 'Nonaktif',
        ];

        // Array untuk komponen filter
        $selects = [
            [
                'name' => 'is_active',
                'label' => 'Semua Status',
                'options' => $statuses,
            ],
        ];

        if ($request->ajax()) {
            return response()->json([
                'data' => $suppliers->items(),
                'links' => (string) $suppliers->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('reports.suppliers.index', [
            'suppliers' => $suppliers->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $suppliers, // tetap simpan pagination untuk tampilkan links
        ]);
    }
}
