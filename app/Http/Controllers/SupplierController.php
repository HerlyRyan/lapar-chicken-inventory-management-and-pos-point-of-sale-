<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Traits\TableFilterTrait;

class SupplierController extends Controller
{
    use TableFilterTrait;
    /**
     * Display a listing of the resource.
     */
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

        return view('suppliers.index', [
            'suppliers' => $suppliers->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $suppliers, // tetap simpan pagination untuk tampilkan links
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code',
            'address' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20|regex:/^62\d{8,13}$/',
            'email' => 'nullable|email|max:255',
            'is_active' => 'sometimes|boolean',
        ], [
            'name.required' => 'Nama supplier wajib diisi.',
            'name.max' => 'Nama supplier maksimal 255 karakter.',
            'code.required' => 'Kode supplier wajib diisi.',
            'code.max' => 'Kode supplier maksimal 50 karakter.',
            'code.unique' => 'Kode supplier sudah digunakan.',
            'address.max' => 'Alamat maksimal 255 karakter.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'phone.regex' => 'Format nomor telepon harus dimulai dengan 62 (contoh: 6282255647148).',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
        ]);
        // Normalize is_active from checkbox (hidden 0 + checked 1)
        $validated['is_active'] = $request->boolean('is_active');
        \App\Models\Supplier::create($validated);
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supplier = \App\Models\Supplier::with('materials')->findOrFail($id);
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supplier = \App\Models\Supplier::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code,'.$id,
            'address' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20|regex:/^62\d{8,13}$/',
            'email' => 'nullable|email|max:255',
            'is_active' => 'sometimes|boolean',
        ], [
            'name.required' => 'Nama supplier wajib diisi.',
            'name.max' => 'Nama supplier maksimal 255 karakter.',
            'code.required' => 'Kode supplier wajib diisi.',
            'code.max' => 'Kode supplier maksimal 50 karakter.',
            'code.unique' => 'Kode supplier sudah digunakan.',
            'address.max' => 'Alamat maksimal 255 karakter.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'phone.regex' => 'Format nomor telepon harus dimulai dengan 62 (contoh: 6282255647148).',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
        ]);
        // Normalize is_active from checkbox pattern
        $validated['is_active'] = $request->boolean('is_active');
        $supplier->update($validated);
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $supplier = \App\Models\Supplier::withCount('materials')->findOrFail($id);
        if ($supplier->materials_count > 0) {
            return back()->with('error', 'Supplier tidak dapat dihapus karena masih memasok bahan mentah. Silakan pindahkan atau hapus semua bahan mentah terlebih dahulu.');
        }
        
        $supplier->delete();
        
        // Jika ada parameter redirect_to, gunakan itu untuk pengalihan
        if ($request->has('redirect_to')) {
            return redirect($request->redirect_to)->with('success', 'Supplier berhasil dihapus.');
        }
        
        // Jika tidak, gunakan pengalihan default ke halaman index
        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(\App\Models\Supplier $supplier)
    {
        $supplier->update(['is_active' => !$supplier->is_active]);
        $status = $supplier->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('suppliers.index')
                        ->with('success', "Supplier berhasil {$status}.");
    }
}
