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

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by request code or purpose
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('request_code', 'like', "%{$search}%")
                  ->orWhere('purpose', 'like', "%{$search}%");
            });
        }

        $productionRequests = $query->paginate(15);

        return view('production-processes.index', compact('productionRequests'));
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
        // Only allow completing in-progress requests
        if (!$productionRequest->isInProgress()) {
            return redirect()->route('production-processes.index')
                ->with('error', 'Hanya produksi yang sedang berlangsung yang dapat diselesaikan.');
        }

        $request->validate([
            'production_notes' => 'nullable|string|max:1000',
            'production_evidence' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            // Realized outputs for the production
            'realized_outputs' => 'required|array',
            'realized_outputs.*' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $productionRequest) {
            // Load planned outputs
            $productionRequest->load('outputs.semiFinishedProduct');

            // Upload the production evidence photo
            if ($request->hasFile('production_evidence')) {
                $file = $request->file('production_evidence');
                $filename = 'production_' . $productionRequest->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('production-evidence', $filename, 'public');
                
                // Save the path to the production request
                $productionRequest->evidence_path = $path;
                $productionRequest->save();
            }

            foreach ($productionRequest->outputs as $output) {
                $product = $output->semiFinishedProduct;
                if (!$product) {
                    continue;
                }

                // Get the realized quantity from the form inputs
                $outputId = $output->id;
                $actualQty = isset($request->realized_outputs[$outputId]) 
                    ? (int) $request->realized_outputs[$outputId] 
                    : (int) $output->planned_quantity;

                // Persist actual quantity
                $output->actual_quantity = $actualQty;
                $output->save();

                // Determine production center branch for this product
                $centerBranchId = $product->managing_branch_id;
                if (!$centerBranchId) {
                    $centerBranchId = Branch::production()->value('id');
                }

                if ($centerBranchId) {
                    // Update branch stock using helper on model
                    $product->updateStockForBranch($centerBranchId, $actualQty, null, 'add');
                }
            }

            // Mark production as completed
            $productionRequest->update([
                'status' => 'completed',
                'production_completed_by' => Auth::id(),
                'production_completed_at' => now(),
                'production_notes' => $request->production_notes
            ]);
        });

        return redirect()->route('production-processes.index')
            ->with('success', 'Produksi berhasil diselesaikan. Hasil produksi telah ditambahkan ke stok pusat produksi.');
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
        
        $plannedOutputs = $productionRequest->outputs->map(function($output) {
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
