<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::query();

        // For column selection
        $columns = [
            ['key' => 'name', 'label' => 'Nama Cabang'],
            ['key' => 'code', 'label' => 'Kode'],
            ['key' => 'type', 'label' => 'Tipe'],
            ['key' => 'address', 'label' => 'Alamat'],
            ['key' => 'phone', 'label' => 'Telepon'],
            ['key' => 'is_active', 'label' => 'Status'],
        ];

        // Search global
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
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

        /** @var \Illuminate\Pagination\LengthAwarePaginator $branch */
        $branch = $query->paginate(10);

        $statuses = [
            1 => 'Aktif',
            0 => 'Nonaktif',
        ];

        // Gabungkan semua ke dalam array untuk komponen filter
        $selects = [
            [
                'name' => 'type',
                'label' => 'Semua Tipe',
                'options' => [
                    'branch' => 'Cabang',
                    'production' => 'Produksi',
                ],
            ],
            [
                'name' => 'is_active',
                'label' => 'Semua Status',
                'options' => $statuses,
            ],
        ];

        if ($request->ajax()) {
            return response()->json([
                'data' => $branch->items(),
                'links' => (string) $branch->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('reports.branches.index', [
            'branches' => $branch->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $branch,
        ]);
    }

    public function print(Request $request)
    {
        $query = Branch::query();

        // Search global
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status = $request->get('is_active')) {
            $query->where('is_active', $status);
        }

        // Filter status
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        // Sorting
        if ($sortBy = $request->get('sort_by')) {
            $sortDir = $request->get('sort_dir', 'asc');
            $query->orderBy($sortBy, $sortDir);
        }
        
        $branch = $query->get();

        return view('reports.branches.print', [
            'branches' => $branch
        ]);
    }
}
