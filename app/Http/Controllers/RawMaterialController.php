<?php

namespace App\Http\Controllers;

use App\Models\{RawMaterial, Unit, Supplier, Branch, Category};
use App\Helpers\{ImageHelper, CodeGeneratorHelper};
use App\Traits\TableFilterTrait;
use Illuminate\Http\{Request, JsonResponse};

class RawMaterialController extends Controller
{
    use TableFilterTrait;
    /**
     * Get latest prices for raw materials
     * Used for real-time polling in purchase order form
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestPrices(): JsonResponse
    {
        // Get all raw materials with their latest prices
        $rawMaterials = RawMaterial::select('id', 'name', 'unit_price', 'updated_at')
            ->get()
            ->map(function($material) {
                return [
                    'id' => $material->id,
                    'name' => $material->name,
                    'unit_price' => $material->unit_price,
                    'updated_at' => $material->updated_at->timestamp
                ];
            });
            
        return response()->json([
            'success' => true,
            'data' => $rawMaterials,
            'timestamp' => now()->timestamp
        ]);
    }

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

        return view('raw-materials.index', [
            'rawMaterials' => $rawMaterials->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $rawMaterials,
        ]);
    }

    public function create()
    {
        $units = Unit::active()->orderBy('unit_name')->get();
        $branches = Branch::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        $missing = [];
        if ($units->count() == 0) $missing[] = 'satuan';
        if ($suppliers->count() == 0) $missing[] = 'supplier';

        if (count($missing) > 0) {
            $missingLinks = [];
            if (in_array('satuan', $missing)) {
                $missingLinks[] = '<a href="' . route('units.index') . '" class="alert-link">kelola satuan</a>';
            }
            if (in_array('supplier', $missing)) {
                $missingLinks[] = '<a href="' . route('suppliers.index') . '" class="alert-link">kelola supplier</a>';
            }
            $message = 'Sebelum menambah bahan mentah, Anda wajib mengisi data: ' . implode(', ', $missing) . 
                      '. Silakan klik link berikut: ' . implode(' atau ', $missingLinks) . '.';
            // Get branch_id from request or session
            $branchId = request('branch_id') ?: session('selected_branch_id');
            return redirect()->route('raw-materials.index', $branchId ? ['branch_id' => $branchId] : [])->with('warning', $message);
        }

        // Get branch context for stock initialization
        $selectedBranch = null;
        $currentBranch = null;
        
        if (request('branch_id')) {
            $selectedBranch = Branch::where('is_active', true)->find(request('branch_id'));
        } elseif (session('selected_branch_id')) {
            $currentBranch = Branch::where('is_active', true)->find(session('selected_branch_id'));
        }

        // Get user's current branch if no branch is selected
        if (!$selectedBranch && !$currentBranch && auth()->check() && auth()->user()->branch) {
            $currentBranch = auth()->user()->branch;
        }

        // Get all branches for selector
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        
        // Get categories for dropdown
        $categories = \App\Models\Category::orderBy('name')->get();

        // Get permission info
        $canSwitchBranch = auth()->check() && auth()->user()->is_superadmin;

        return view('raw-materials.create', compact('units', 'branches', 'suppliers', 'selectedBranch', 'currentBranch', 'canSwitchBranch', 'categories'))->with('showBranchSelector', true);
    }

    public function store(Request $request)
    {
        // Make code field optional since we'll generate it if it's empty
        $codeValidation = $request->filled('code') ? 'string|max:50|unique:raw_materials,code' : '';
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => $codeValidation,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:500',
            'unit_id' => 'required|exists:units,id',
            'minimum_stock' => 'nullable|numeric|min:0',
            'current_stock' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Nama bahan mentah wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'unit_id.required' => 'Satuan wajib dipilih.',
            'unit_id.exists' => 'Satuan yang dipilih tidak valid.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'code.unique' => 'Kode bahan baku sudah digunakan.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/materials'), $filename);
            $validated['image'] = 'storage/materials/' . $filename;
        }
        
        // Generate unique code if not provided
        if (!$request->filled('code')) {
            $validated['code'] = CodeGeneratorHelper::generateProductCode('RM', $validated['name'], RawMaterial::class);
        }

        // Create the raw material
        $rawMaterial = RawMaterial::create($validated);

        // No need for branch stock initialization as we're using central stock management

        // Get branch_id from request or session
        $branchId = request('branch_id') ?: session('selected_branch_id');
        return redirect()->route('raw-materials.index', $branchId ? ['branch_id' => $branchId] : [])
            ->with('success', __('messages.raw_material_created'));
    }

    public function show(RawMaterial $rawMaterial)
    {
        $rawMaterial->load(['unit']);
        
        // Get branch context for display
        $selectedBranch = null;
        $currentBranch = null;
        
        if (request('branch_id')) {
            $selectedBranch = Branch::find(request('branch_id'));
        } elseif (session('selected_branch_id')) {
            $currentBranch = Branch::find(session('selected_branch_id'));
        }
        
        return view('raw-materials.show', compact('rawMaterial', 'selectedBranch', 'currentBranch'));
    }

    public function edit(RawMaterial $rawMaterial)
    {
        $units = Unit::active()->orderBy('unit_name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $categories = \App\Models\Category::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        
        // Get branch context for editing
        $selectedBranch = null;
        $currentBranch = null;
        
        if (request('branch_id')) {
            $selectedBranch = Branch::find(request('branch_id'));
        } elseif (session('selected_branch_id')) {
            $currentBranch = Branch::find(session('selected_branch_id'));
        }
        
        return view('raw-materials.edit', compact('rawMaterial', 'units', 'branches', 'suppliers', 'selectedBranch', 'currentBranch', 'categories'));
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:raw_materials,code,' . $rawMaterial->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:500',
            'unit_id' => 'required|exists:units,id',
            'minimum_stock' => 'nullable|numeric|min:0',
            'current_stock' => 'nullable|numeric|min:0',
            'unit_price' => 'nullable|numeric|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Nama bahan mentah wajib diisi.',
            'unit_id.required' => 'Satuan wajib dipilih.',
            'unit_id.exists' => 'Satuan yang dipilih tidak valid.',

            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'code.required' => 'Kode bahan baku wajib diisi.',
            'code.unique' => 'Kode bahan baku sudah digunakan.',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($rawMaterial->image && file_exists(public_path($rawMaterial->image))) {
                unlink(public_path($rawMaterial->image));
            }
            
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/materials'), $filename);
            $validated['image'] = 'storage/materials/' . $filename;
        }

        $rawMaterial->update($validated);

        // Get branch_id from request or session
        $branchId = request('branch_id') ?: session('selected_branch_id');
        return redirect()->route('raw-materials.index', $branchId ? ['branch_id' => $branchId] : [])
            ->with('success', 'Bahan mentah berhasil diperbarui.');
    }

    public function destroy(RawMaterial $rawMaterial)
    {
                $rawMaterial->delete();

        // Get branch_id from request or session
        $branchId = request('branch_id') ?: session('selected_branch_id');
        return redirect()->route('raw-materials.index', $branchId ? ['branch_id' => $branchId] : [])
            ->with('success', __('messages.raw_material_deleted'));
    }

    /**
     * Raw materials use centralized stock - no branch initialization needed
     */
    public function initializeBranchStocks(RawMaterial $rawMaterial)
    {
        return response()->json([
            'success' => true,
            'message' => 'Bahan mentah menggunakan stok terpusat, tidak perlu inisialisasi per cabang'
        ]);
    }

    /**
     * Add stock to centralized raw material stock
     */
    public function addBranchStock(RawMaterial $rawMaterial, $branchId = null)
    {
        try {
            $quantity = request('quantity', 0);
            
            if ($quantity <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah harus lebih dari 0'
                ], 400);
            }

            $rawMaterial->current_stock += $quantity;
            $rawMaterial->save();

            return response()->json([
                'success' => true,
                'message' => "Berhasil menambah stok sebanyak {$quantity}",
                'new_stock' => $rawMaterial->current_stock
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah stok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reduce stock from centralized raw material stock
     */
    public function reduceBranchStock(RawMaterial $rawMaterial, $branchId = null)
    {
        try {
            $quantity = request('quantity', 0);
            
            if ($quantity <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah harus lebih dari 0'
                ], 400);
            }

            if ($rawMaterial->current_stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok saat ini: ' . $rawMaterial->current_stock
                ], 400);
            }

            $rawMaterial->current_stock -= $quantity;
            $rawMaterial->save();

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengurangi stok sebanyak {$quantity}",
                'new_stock' => $rawMaterial->current_stock
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengurangi stok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set minimum stock for specific branch
     */
    public function setMinimumStock(RawMaterial $rawMaterial, $branchId)
    {
        try {
            $request = request();
            $minimumStock = $request->input('minimum_stock');

            if ($minimumStock < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok minimum tidak boleh kurang dari 0'
                ], 400);
            }

            $branchStock = \App\Models\BranchStock::where([
                'branch_id' => $branchId,
                'item_type' => 'material',
                'item_id' => $rawMaterial->id
            ])->first();

            if (!$branchStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok cabang tidak ditemukan'
                ], 404);
            }

            $branchStock->minimum_stock = $minimumStock;
            $branchStock->last_updated = now();
            $branchStock->save();

            return response()->json([
                'success' => true,
                'message' => "Berhasil mengatur stok minimum menjadi {$minimumStock}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengatur stok minimum: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(RawMaterial $rawMaterial)
    {
        $rawMaterial->update(['is_active' => !$rawMaterial->is_active]);
        $status = $rawMaterial->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('raw-materials.index')
                        ->with('success', "Bahan mentah berhasil {$status}.");
    }
}
