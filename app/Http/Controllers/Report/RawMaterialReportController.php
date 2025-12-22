<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class RawMaterialReportController extends Controller
{
    public function index(Request $request)
    {
        $query = RawMaterial::with(['unit', 'supplier', 'category']);

        $columns = [
            ['key' => 'code', 'label' => 'Kode'],
            ['key' => 'image', 'label' => 'Gambar'],
            ['key' => 'name', 'label' => 'Nama Bahan Baku'],
            ['key' => 'category', 'label' => 'Kategori'],
            ['key' => 'unit', 'label' => 'Satuan'],
            ['key' => 'current_stock', 'label' => 'Stok Saat Ini'],
            ['key' => 'minimum_stock', 'label' => 'Stok Minimum'],
            ['key' => 'unit_price', 'label' => 'Harga Satuan'],
            ['key' => 'supplier', 'label' => 'Supplier'],
            ['key' => 'is_active', 'label' => 'Status'],
        ];

        // === SEARCH ===
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('unit', fn($q2) => $q2->where('unit_name', 'like', "%{$search}%"))
                    ->orWhereHas('supplier', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
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
                    $query->leftjoin('categories', 'categories.id', '=', 'raw_materials.category_id')
                        ->orderBy('categories.name', $sortDir)
                        ->select('raw_materials.*');
                    break;

                case 'unit':
                    $query->leftjoin('units', 'units.id', '=', 'raw_materials.unit_id')
                        ->orderBy('units.unit_name', $sortDir)
                        ->select('raw_materials.*');
                    break;

                case 'supplier':
                    $query->leftjoin('suppliers', 'suppliers.id', '=', 'raw_materials.supplier_id')
                        ->orderBy('suppliers.name', $sortDir)
                        ->select('raw_materials.*');
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $rawMaterials */
        $rawMaterials = $query->paginate(10);

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
                'data' => $rawMaterials->items(),
                'links' => (string) $rawMaterials->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('reports.raw-materials.index', [
            'rawMaterials' => $rawMaterials->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $rawMaterials,
        ]);
    }

    public function print(Request $request)
    {
        $query = RawMaterial::with(['unit', 'supplier', 'category']);        

        // === SEARCH ===
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('unit', fn($q2) => $q2->where('unit_name', 'like', "%{$search}%"))
                    ->orWhereHas('supplier', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
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
                    $query->leftjoin('categories', 'categories.id', '=', 'raw_materials.category_id')
                        ->orderBy('categories.name', $sortDir)
                        ->select('raw_materials.*');
                    break;

                case 'unit':
                    $query->leftjoin('units', 'units.id', '=', 'raw_materials.unit_id')
                        ->orderBy('units.unit_name', $sortDir)
                        ->select('raw_materials.*');
                    break;

                case 'supplier':
                    $query->leftjoin('suppliers', 'suppliers.id', '=', 'raw_materials.supplier_id')
                        ->orderBy('suppliers.name', $sortDir)
                        ->select('raw_materials.*');
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        }

        $rawMaterials = $query->get();       

        return view('reports.raw-materials.print', [
            'rawMaterials' => $rawMaterials,
        ]);
    }
}
