<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionRequest;
use App\Models\SemiFinishedProduct;
use App\Models\ProductionRequestOutput;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductionProcessController extends Controller
{
    /**
     * Display a listing of production requests ready for processing
     */
    public function index(Request $request)
    {
        $query = ProductionRequest::with(['requestedBy', 'approvedBy', 'productionStartedBy', 'items.rawMaterial.unit'])
            ->whereIn('status', ['approved', 'in_progress', 'completed'])
            ->orderBy('approved_at', 'asc');

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

        $productionRequests = $query->paginate(15);

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

        return view('production-processes.index', compact('productionRequests', 'selects', 'columns'));
    }

    /**
     * Display the specified production request for processing
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

        return view('production-processes.show', compact('productionRequest'));
    }

    /**
     * Start production process
     */
    public function start(Request $request, ProductionRequest $productionRequest)
    {
        // Only allow starting approved requests
        if (!$productionRequest->isApproved()) {
            return redirect()->route('production-processes.index')
                ->with('error', 'Hanya pengajuan yang sudah disetujui yang dapat dimulai produksinya.');
        }

        $request->validate([
            'production_notes' => 'nullable|string|max:1000'
        ]);

        $productionRequest->update([
            'status' => 'in_progress',
            'production_started_by' => Auth::id(),
            'production_started_at' => now(),
            'production_notes' => $request->production_notes
        ]);

        return redirect()->route('production-processes.index')
            ->with('success', 'Produksi telah dimulai. Status telah diubah menjadi "Sedang Diproduksi".');
    }

    /**
     * Update production status
     */
    public function updateStatus(Request $request, ProductionRequest $productionRequest)
    {
        // Only allow updating in-progress requests
        if (!$productionRequest->isInProgress()) {
            return redirect()->route('production-processes.index')
                ->with('error', 'Hanya produksi yang sedang berlangsung yang dapat diupdate statusnya.');
        }

        $request->validate([
            'production_notes' => 'required|string|max:1000'
        ]);

        $productionRequest->update([
            'production_notes' => $request->production_notes
        ]);

        return redirect()->route('production-processes.index')
            ->with('success', 'Status produksi berhasil diperbarui.');
    }

    /**
     * Complete production process
     */
    public function complete(Request $request, ProductionRequest $productionRequest)
    {
        // 🔍 DEBUG AMAN (boleh dihapus setelah fix)
        // dd($request);

        // ✅ VALIDASI STATE AWAL (PAKAI RAW STATUS)
        if (!$productionRequest->isInProgress()) {
            return back()->with('error', 'Produksi tidak dalam status berjalan.');
        }

        // ✅ VALIDASI REQUEST
        $validated = $request->validate([
            'production_notes'     => 'nullable|string|max:1000',
            'production_evidence'  => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'realized_outputs'     => 'nullable|array',
            'realized_outputs.*'   => 'nullable|integer|min:0',
        ]);

        // ✅ UPLOAD FILE DI LUAR TRANSACTION
        $evidencePath = $request->file('production_evidence')
            ->store('production-evidence', 'public');

        try {
            DB::transaction(function () use (
                $productionRequest,
                $validated,
                $evidencePath
            ) {
                // 🔒 LOCK DATA PRODUKSI
                $production = ProductionRequest::query()
                    ->whereKey($productionRequest->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // 📝 UPDATE BUKTI & CATATAN
                $production->update([
                    'evidence_path'    => $evidencePath,
                    'production_notes' => $validated['production_notes'] ?? null,
                ]);

                // 🔄 LOAD OUTPUT
                $production->load('outputs.semiFinishedProduct');

                foreach ($production->outputs as $output) {
                    $product = $output->semiFinishedProduct;

                    if (!$product) {
                        throw new \RuntimeException(
                            "Produk semi-finished tidak ditemukan (output_id: {$output->id})"
                        );
                    }

                    $actualQty = (int) $validated['realized_outputs'][$output->id];

                    // 📦 UPDATE OUTPUT AKTUAL
                    $output->update([
                        'actual_quantity' => $actualQty,
                    ]);

                    // 🏭 TENTUKAN BRANCH PRODUKSI
                    $branchId = $product->managing_branch_id
                        ?? Branch::production()->value('id');

                    if (!$branchId) {
                        throw new \RuntimeException('Branch produksi tidak ditemukan');
                    }

                    // ➕ UPDATE STOK
                    $product->updateStockForBranch(
                        $branchId,
                        $actualQty,
                        null,
                        'add'
                    );
                }

                // ✅ FINAL: UPDATE STATUS
                $production->update([
                    'status'                   => "completed",
                    'production_completed_by'  => auth()->id(),
                    'production_completed_at'  => now(),
                ]);
            });

            return redirect()
                ->route('production-processes.index')
                ->with('success', 'Produksi berhasil diselesaikan.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Terjadi kesalahan saat menyelesaikan produksi.');
        }
    }


    /**
     * Get planned outputs for a production request as JSON
     */
    public function getPlannedOutputs(ProductionRequest $productionRequest)
    {
        // Only allow fetching planned outputs for in-progress requests
        if (!$productionRequest->isInProgress()) {
            return response()->json([
                'error' => 'Hanya dapat melihat output yang direncanakan untuk produksi yang sedang berlangsung.'
            ], 400);
        }

        $productionRequest->load('outputs.semiFinishedProduct.unit');

        $plannedOutputs = $productionRequest->outputs->map(function ($output) {
            return [
                'id' => $output->id,
                'product_id' => $output->semi_finished_product_id,
                'product_name' => $output->semiFinishedProduct->name ?? 'Produk tidak ditemukan',
                'quantity' => $output->planned_quantity,
                // SemiFinishedProduct::getUnitAttribute() returns the unit name string
                'unit' => $output->semiFinishedProduct ? ($output->semiFinishedProduct->unit ?? '-') : '-',
                'unit_cost' => $output->unit_cost,
                'total_cost' => $output->planned_quantity * $output->unit_cost,
            ];
        });

        return response()->json($plannedOutputs);
    }
}
