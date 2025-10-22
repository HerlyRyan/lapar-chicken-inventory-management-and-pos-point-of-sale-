<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SemiFinishedProduct;
use App\Models\FinishedBranchStock;
use App\Models\FinishedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

        return view('branches.index', [
            'branches' => $branch->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $branch,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:branches,code',
            'type' => 'required|in:branch,production',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
                        'phone' => 'nullable|regex:/^62\d{8,13}$/',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama cabang wajib diisi.',
            'name.max' => 'Nama cabang maksimal 100 karakter.',
            'code.required' => 'Kode cabang wajib diisi.',
            'code.max' => 'Kode cabang maksimal 20 karakter.',
            'code.unique' => 'Kode cabang sudah digunakan.',
            'type.required' => 'Tipe cabang wajib dipilih.',
            'type.in' => 'Tipe cabang tidak valid.',
            'address.max' => 'Alamat maksimal 255 karakter.',
                        'phone.max' => 'Telepon maksimal 20 karakter.',
            'phone.regex' => 'Format nomor telepon harus dimulai dengan 62 (contoh: 6282255647148).',
            'email.email' => 'Email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'is_active.boolean' => 'Status aktif tidak valid.',
        ]);
        
        Branch::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'type' => $request->type,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $branch = Branch::findOrFail($id);
        return view('branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:branches,code,' . $branch->id,
            'type' => 'required|in:branch,production',
            'address' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
                        'phone' => 'nullable|regex:/^62\d{8,13}$/',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama cabang wajib diisi.',
            'name.max' => 'Nama cabang maksimal 100 karakter.',
            'code.required' => 'Kode cabang wajib diisi.',
            'code.max' => 'Kode cabang maksimal 20 karakter.',
            'code.unique' => 'Kode cabang sudah digunakan.',
            'type.required' => 'Tipe cabang wajib dipilih.',
            'type.in' => 'Tipe cabang tidak valid.',
            'address.max' => 'Alamat maksimal 255 karakter.',
                        'phone.max' => 'Telepon maksimal 20 karakter.',
            'phone.regex' => 'Format nomor telepon harus dimulai dengan 62 (contoh: 6282255647148).',
            'email.email' => 'Email tidak valid.',
            'email.max' => 'Email maksimal 255 karakter.',
            'is_active.boolean' => 'Status aktif tidak valid.',
        ]);
        
        $branch->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'type' => $request->type,
            'is_active' => $request->has('is_active'),
        ]);
        
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dihapus.');
    }
    
    /**
     * Switch user's active branch (for Super Admin)
     */
    public function switchBranch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id'
        ]);

        $user = Auth::user();
        
        // Update user's current branch context
        $branch = Branch::findOrFail($request->branch_id);
        $user->update(['branch_id' => $branch->id]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil beralih ke cabang: {$branch->name}",
            'branch' => [
                'id' => $branch->id,
                'name' => $branch->name,
                'code' => $branch->code
            ]
        ]);
    }

    /**
     * Get branch inventory summary
     */
    public function getInventorySummary($branchId)
    {
        $branch = Branch::findOrFail($branchId);
        
        // Get stock summary using direct models
        $finishedStocks = \App\Models\FinishedBranchStock::where('branch_id', $branchId)->count();
        $semiFinishedStocks = \App\Models\SemiFinishedBranchStock::where('branch_id', $branchId)->count();
        
        $summary = [
            'finished_products' => $finishedStocks,
            'semi_finished_products' => $semiFinishedStocks,
            'raw_materials' => 0 // Raw materials are centralized
        ];

        return response()->json([
            'success' => true,
            'branch' => $branch,
            'summary' => $summary
        ]);
    }

    /**
     * API endpoint to get stock for specific item in specific branch
     */
    public function getItemStock($itemType, $itemId, $branchId)
    {
        try {
            $stock = 0;
            $unitAbbr = null;
            
            switch ($itemType) {
                case 'finished':
                    $branchStock = FinishedBranchStock::where('branch_id', $branchId)
                        ->where('finished_product_id', $itemId)
                        ->first();
                    $stock = $branchStock ? $branchStock->quantity : 0;
                    // Load unit abbreviation
                    $product = FinishedProduct::with('unit:id,abbreviation')->find($itemId);
                    $unitAbbr = $product && $product->unit ? $product->unit->abbreviation : null;
                    break;
                    
                case 'semi-finished':
                    $semiFinishedProduct = SemiFinishedProduct::with('unit:id,abbreviation')->find($itemId);
                    if ($semiFinishedProduct) {
                        $branchStock = $semiFinishedProduct->getStockForBranch($branchId);
                        $stock = $branchStock ? $branchStock->quantity : 0;
                    } else {
                        $stock = 0;
                    }
                    // Use relation explicitly to avoid accessor shadowing
                    $unitRel = ($semiFinishedProduct && $semiFinishedProduct->relationLoaded('unit')) ? $semiFinishedProduct->getRelation('unit') : null;
                    $unitAbbr = $unitRel ? $unitRel->abbreviation : null;
                    break;
                    
                default:
                    return response()->json(['error' => 'Invalid item type'], 400);
            }
            
            return response()->json(['stock' => $stock, 'unit_abbr' => $unitAbbr]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch stock'], 500);
        }
    }

    /**
     * Toggle the active status of the specified resource.
     */
    public function toggleStatus(Branch $branch)
    {
        $branch->update(['is_active' => !$branch->is_active]);
        $status = $branch->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('branches.index')
                        ->with('success', "Cabang berhasil {$status}.");
    }
}
