<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionRequest;
use App\Models\ProductionRequestItem;
use App\Models\ProductionRequestOutput;
use App\Models\RawMaterial;
use App\Models\SemiFinishedProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductionRequestController extends Controller
{
    /**
     * Display a listing of production requests
     */
    public function index(Request $request)
    {
        $query = ProductionRequest::with(['requestedBy', 'items.rawMaterial']);

        $columns = [
            ['key' => 'request_code', 'label' => 'Kode Pengajuan'],
            ['key' => 'purpose', 'label' => 'Peruntukan'],
            ['key' => 'requested_by', 'label' => 'Pemohon'],
            ['key' => 'total_raw_material_cost', 'label' => 'Total Biaya'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'created_at', 'label' => 'Tanggal'],
        ];

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by request code or purpose
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%");
            });
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

        /** @var \Illuminate\Pagination\LengthAwarePaginator $productionRequests */
        $productionRequests = $query->paginate(10);

        $statuses = [
            'pending' => 'Ditunda',
            'approved' => 'Diterima',
            'rejected' => 'Ditolak',
            'in_progress' => 'Dalam Proses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        $selects = [
            [
                'name' => 'status',
                'label' => 'Semua Status',
                'options' => $statuses,
            ],
        ];

        // === RESPONSE ===
        if ($request->ajax()) {
            return response()->json([
                'data' => $productionRequests->items(),
                'links' => (string) $productionRequests->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('production-requests.index', compact('productionRequests', 'columns', 'selects'));
    }

    /**
     * Show the form for creating a new production request
     */
    public function create()
    {
        $rawMaterials = RawMaterial::where('is_active', true)
            ->with('unit')
            ->orderBy('name')
            ->get();

        $semiFinishedProducts = SemiFinishedProduct::where('is_active', true)
            ->with('unit')
            ->orderBy('name')
            ->get();

        return view('production-requests.create', compact('rawMaterials', 'semiFinishedProducts'));
    }

    /**
     * Store a newly created production request
     */
    public function store(Request $request)
    {
        $request->validate([
            'purpose' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.requested_quantity' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
            // Planned outputs (optional but recommended)
            'outputs' => 'nullable|array|min:1',
            'outputs.*.semi_finished_product_id' => 'required_with:outputs|exists:semi_finished_products,id',
            'outputs.*.planned_quantity' => 'required_with:outputs|numeric|min:0.001',
            'outputs.*.notes' => 'nullable|string|max:1000',
        ]);

        // Enforce each requested quantity does not exceed current stock
        $rawIds = collect($request->items)->pluck('raw_material_id')->unique()->all();
        $materials = RawMaterial::whereIn('id', $rawIds)->get()->keyBy('id');
        $errors = [];
        foreach ($request->items as $i => $item) {
            $rm = $materials[$item['raw_material_id']] ?? null;
            if ($rm && (float)$item['requested_quantity'] > (float)$rm->current_stock) {
                $name = $rm->name ?? 'Bahan';
                $available = (float)$rm->current_stock;
                $errors["items.$i.requested_quantity"] = "Jumlah melebihi stok tersedia ($name: $available). Maksimal sesuai stok saat ini.";
            }
        }
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        DB::transaction(function () use ($request) {
            // Create production request
            $productionRequest = ProductionRequest::create([
                'request_code' => ProductionRequest::generateRequestCode(),
                'requested_by' => Auth::id(),
                'purpose' => $request->input('purpose', ''),
                'notes' => $request->notes,
                'status' => 'pending'
            ]);

            $totalCost = 0;

            // Create production request items
            foreach ($request->items as $item) {
                $requestItem = ProductionRequestItem::create([
                    'production_request_id' => $productionRequest->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'requested_quantity' => $item['requested_quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'notes' => $item['notes'] ?? null
                ]);

                $totalCost += $requestItem->total_cost;
            }

            // Update total cost
            $productionRequest->update(['total_raw_material_cost' => $totalCost]);

            // Save planned outputs if provided
            if ($request->filled('outputs')) {
                foreach ($request->outputs as $output) {
                    ProductionRequestOutput::create([
                        'production_request_id' => $productionRequest->id,
                        'semi_finished_product_id' => $output['semi_finished_product_id'],
                        'planned_quantity' => $output['planned_quantity'],
                        'notes' => $output['notes'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('production-requests.index')
            ->with('success', 'Pengajuan produksi berhasil dibuat dan akan dikirim ke manajer untuk persetujuan.');
    }

    /**
     * Display the specified production request
     */
    public function show(ProductionRequest $productionRequest)
    {
        $productionRequest->load([
            'requestedBy',
            'approvedBy',
            'productionStartedBy',
            'productionCompletedBy',
            'items.rawMaterial.unit',
            'outputs.semiFinishedProduct.unit'
        ]);

        return view('production-requests.show', compact('productionRequest'));
    }

    /**
     * Show the form for editing the specified production request
     */
    public function edit(ProductionRequest $productionRequest)
    {
        // Only allow editing if status is pending
        if (!$productionRequest->isPending()) {
            return redirect()->route('production-requests.index')
                ->with('error', 'Hanya pengajuan dengan status "Menunggu Persetujuan" yang dapat diedit.');
        }

        $rawMaterials = RawMaterial::where('is_active', true)
            ->with('unit')
            ->orderBy('name')
            ->get();

        $semiFinishedProducts = SemiFinishedProduct::where('is_active', true)
            ->with('unit')
            ->orderBy('name')
            ->get();

        $productionRequest->load(['items.rawMaterial.unit', 'outputs.semiFinishedProduct.unit']);

        return view('production-requests.edit', compact('productionRequest', 'rawMaterials', 'semiFinishedProducts'));
    }

    /**
     * Update the specified production request
     */
    public function update(Request $request, ProductionRequest $productionRequest)
    {
        // Only allow updating if status is pending
        if (!$productionRequest->isPending()) {
            return redirect()->route('production-requests.index')
                ->with('error', 'Hanya pengajuan dengan status "Menunggu Persetujuan" yang dapat diubah.');
        }

        $request->validate([
            'purpose' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.requested_quantity' => 'required|numeric|min:0.001',
            'items.*.unit_cost' => 'required|numeric|min:0',
            // Planned outputs (optional but recommended)
            'outputs' => 'nullable|array|min:1',
            'outputs.*.semi_finished_product_id' => 'required_with:outputs|exists:semi_finished_products,id',
            'outputs.*.planned_quantity' => 'required_with:outputs|numeric|min:0.001',
            'outputs.*.notes' => 'nullable|string|max:1000',
        ]);

        // Enforce each requested quantity does not exceed current stock
        $rawIds = collect($request->items)->pluck('raw_material_id')->unique()->all();
        $materials = RawMaterial::whereIn('id', $rawIds)->get()->keyBy('id');
        $errors = [];
        foreach ($request->items as $i => $item) {
            $rm = $materials[$item['raw_material_id']] ?? null;
            if ($rm && (float)$item['requested_quantity'] > (float)$rm->current_stock) {
                $name = $rm->name ?? 'Bahan';
                $available = (float)$rm->current_stock;
                $errors["items.$i.requested_quantity"] = "Jumlah melebihi stok tersedia ($name: $available). Maksimal sesuai stok saat ini.";
            }
        }
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        DB::transaction(function () use ($request, $productionRequest) {
            // Update production request
            $productionRequest->update([
                'purpose' => $request->input('purpose', ''),
                'notes' => $request->notes
            ]);

            // Delete existing items
            $productionRequest->items()->delete();

            $totalCost = 0;

            // Create new items
            foreach ($request->items as $item) {
                $requestItem = ProductionRequestItem::create([
                    'production_request_id' => $productionRequest->id,
                    'raw_material_id' => $item['raw_material_id'],
                    'requested_quantity' => $item['requested_quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'notes' => $item['notes'] ?? null
                ]);

                $totalCost += $requestItem->total_cost;
            }

            // Update total cost
            $productionRequest->update(['total_raw_material_cost' => $totalCost]);

            // Recreate planned outputs
            $productionRequest->outputs()->delete();
            if ($request->filled('outputs')) {
                foreach ($request->outputs as $output) {
                    ProductionRequestOutput::create([
                        'production_request_id' => $productionRequest->id,
                        'semi_finished_product_id' => $output['semi_finished_product_id'],
                        'planned_quantity' => $output['planned_quantity'],
                        'notes' => $output['notes'] ?? null,
                    ]);
                }
            }
        });

        return redirect()->route('production-requests.index')
            ->with('success', 'Pengajuan produksi berhasil diperbarui.');
    }

    /**
     * Remove the specified production request
     */
    public function destroy(ProductionRequest $productionRequest)
    {
        // Only allow deletion if status is pending
        if (!$productionRequest->isPending()) {
            return redirect()->route('production-requests.index')
                ->with('error', 'Hanya pengajuan dengan status "Menunggu Persetujuan" yang dapat dihapus.');
        }

        $productionRequest->delete();

        return redirect()->route('production-requests.index')
            ->with('success', 'Pengajuan produksi berhasil dihapus.');
    }

    /**
     * Show confirmation page before deleting a production request.
     *
     * @param  \App\Models\ProductionRequest  $productionRequest
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\View\View
     */
    public function deleteConfirm(ProductionRequest $productionRequest)
    {
        if (!$productionRequest->isPending()) {
            return redirect()->route('production-requests.index')
                ->with('error', 'Hanya pengajuan dengan status "Menunggu Persetujuan" yang dapat dihapus.');
        }
        return view('production-requests.delete-confirm', compact('productionRequest'));
    }

    /**
     * Alternative GET method to remove the specified production request (for debugging)
     */
    public function destroyGet(ProductionRequest $productionRequest)
    {
        // Only allow deletion if status is pending
        if (!$productionRequest->isPending()) {
            return redirect()->route('production-requests.index')
                ->with('error', 'Hanya pengajuan dengan status "Menunggu Persetujuan" yang dapat dihapus.');
        }

        $productionRequest->delete();

        return redirect()->route('production-requests.index')
            ->with('success', 'Pengajuan produksi berhasil dihapus via GET.');
    }
}
