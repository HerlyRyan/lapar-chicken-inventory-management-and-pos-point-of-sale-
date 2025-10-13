<?php
// This controller is deprecated. Use RawMaterialController instead.

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Material::query();
        
        if (request('q')) {
            $q = request('q');
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('code', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%");
            });
        }
        
        if (request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }
        
        $materials = $query->orderBy('name')->paginate(15)->withQueryString();
        
        return view('materials.index', compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $units = \App\Models\Unit::active()->orderBy('unit_name')->get();
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        $categories = [
            'raw_material' => 'Bahan Mentah',
            'semi_finished' => 'Bahan Setengah Jadi',
            'finished_product' => 'Produk Siap Jual',
        ];

        $missing = [];
        $missingLinks = [];
        
        if ($units->count() == 0) {
            $missing[] = 'satuan';
            $missingLinks[] = '<a href="' . route('units.index') . '" class="alert-link">kelola satuan</a>';
        }
        if ($suppliers->count() == 0) {
            $missing[] = 'supplier';
            $missingLinks[] = '<a href="' . route('suppliers.index') . '" class="alert-link">kelola supplier</a>';
        }

        if (count($missing) > 0) {
            $message = 'Sebelum menambah data barang, Anda wajib mengisi data: ' . implode(', ', $missing) . 
                      '. Silakan klik link berikut: ' . implode(' atau ', $missingLinks) . '.';
            return redirect()->route('materials.index')->with('warning', $message);
        }

        return view('materials.create', compact('units', 'suppliers', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:materials,code',
            'description' => 'nullable|string|max:500',
            'unit' => 'required|string|max:50',
            'supplier_id' => 'required|exists:suppliers,id',
            'category' => 'required|in:raw_material,semi_finished,finished_product',
            'minimum_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama bahan baku wajib diisi.',
            'name.max' => 'Nama bahan baku maksimal 255 karakter.',
            'code.max' => 'Kode maksimal 50 karakter.',
            'code.unique' => 'Kode sudah digunakan.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'unit.required' => 'Satuan wajib dipilih.',
            'unit.max' => 'Satuan maksimal 50 karakter.',
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'supplier_id.exists' => 'Supplier tidak valid.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'minimum_stock.numeric' => 'Stok minimum harus berupa angka.',
            'minimum_stock.min' => 'Stok minimum tidak boleh negatif.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Material::create($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        return view('materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        $units = \App\Models\Unit::active()->orderBy('unit_name')->get();
        $suppliers = \App\Models\Supplier::orderBy('name')->get();
        $categories = [
            'raw_material' => 'Bahan Mentah',
            'semi_finished' => 'Bahan Setengah Jadi',
            'finished_product' => 'Produk Siap Jual',
        ];
        
        return view('materials.edit', compact('material', 'units', 'suppliers', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:materials,code,' . $material->id,
            'description' => 'nullable|string|max:500',
            'unit' => 'required|string|max:50',
            'supplier_id' => 'required|exists:suppliers,id',
            'category' => 'required|in:raw_material,semi_finished,finished_product',
            'minimum_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama bahan baku wajib diisi.',
            'name.max' => 'Nama bahan baku maksimal 255 karakter.',
            'code.max' => 'Kode maksimal 50 karakter.',
            'code.unique' => 'Kode sudah digunakan.',
            'description.max' => 'Deskripsi maksimal 500 karakter.',
            'unit.required' => 'Satuan wajib dipilih.',
            'unit.max' => 'Satuan maksimal 50 karakter.',
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'supplier_id.exists' => 'Supplier tidak valid.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'minimum_stock.numeric' => 'Stok minimum harus berupa angka.',
            'minimum_stock.min' => 'Stok minimum tidak boleh negatif.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $material->update($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Bahan baku berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()->route('materials.index')
            ->with('success', 'Bahan baku berhasil dihapus.');
    }
}
