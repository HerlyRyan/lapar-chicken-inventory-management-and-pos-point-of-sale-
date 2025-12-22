<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\SalesPackage;
use Illuminate\Http\Request;

class SalePackagesReportController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesPackage::with(['packageItems.finishedProduct', 'creator', 'category']);

        $columns = [
            ['key' => 'name', 'label' => 'Paket'],
            ['key' => 'category', 'label' => 'Kategori'],
            ['key' => 'package_items', 'label' => 'Komponen'],
            ['key' => 'base_price', 'label' => 'Harga Dasar'],
            ['key' => 'discount_amount', 'label' => 'Diskon'],
            ['key' => 'additional_charge', 'label' => 'Tambahan Harga'],
            ['key' => 'final_price', 'label' => 'Harga Jual'],
            ['key' => 'is_active', 'label' => 'Status'],
        ];

        // === SEARCH ===
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
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
                    $query->leftjoin('categories', 'categories.id', '=', 'sales_packages.category_id')
                        ->orderBy('categories.name', $sortDir)
                        ->select('sales_packages.*');
                    break;

                case 'package_items':
                    // Sort berdasarkan jumlah item dalam paket
                    $query->withCount('packageItems')
                        ->orderBy('package_items_count', $sortDir);
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $salesPackages */
        $salesPackages = $query->paginate(10);

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
                'data' => $salesPackages->items(),
                'links' => (string) $salesPackages->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('reports.sale-packages.index', [
            'salesPackages' => $salesPackages->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $salesPackages,
        ]);
    }

    public function print(Request $request)
    {
        $query = SalesPackage::with(['packageItems.finishedProduct', 'creator', 'category']);

        // === SEARCH ===
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
            });
        }

        // === FILTER STATUS ===
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // === SORTING ===
        if ($sortBy = $request->get('sort_by')) {
            $sortDir = $request->get('sort_dir', 'asc');

            // 🧩 Deteksi kolom relasi
            switch ($sortBy) {
                case 'category':
                    $query->leftjoin('categories', 'categories.id', '=', 'sales_packages.category_id')
                        ->orderBy('categories.name', $sortDir)
                        ->select('sales_packages.*');
                    break;

                case 'package_items':
                    // Sort berdasarkan jumlah item dalam paket
                    $query->withCount('packageItems')
                        ->orderBy('package_items_count', $sortDir);
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        }

        $salesPackages = $query->get();

        return view('reports.sale-packages.print', [
            'salesPackages' => $salesPackages,
        ]);
    }
}
