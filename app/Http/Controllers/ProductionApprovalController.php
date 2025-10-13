<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionRequest;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductionApprovalController extends Controller
{
    /**
     * Display a listing of production requests pending approval
     */
    public function index(Request $request)
    {
        $query = ProductionRequest::with(['requestedBy', 'items.rawMaterial.unit']);

        // Sorting (whitelisted columns only)
        $allowedSorts = ['created_at', 'approved_at', 'request_code', 'total_raw_material_cost', 'status'];
        $sortBy = in_array($request->get('sort_by'), $allowedSorts, true) ? $request->get('sort_by') : 'created_at';
        $sortDir = $request->get('sort_dir') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

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

        return view('production-approvals.index', compact('productionRequests'));
    }

    /**
     * Display the specified production request for approval.
     * If requested via AJAX or with ?modal=1, return a partial suitable for a modal.
     * Otherwise, redirect back to index (we use modal-only UX for detail view).
     */
    public function show(Request $request, ProductionRequest $productionRequest)
    {
        $productionRequest->load([
            'requestedBy', 
            'approvedBy', 
            'items.rawMaterial.unit'
        ]);

        // Check if there's sufficient stock for all requested materials
        $stockValidation = [];
        foreach ($productionRequest->items as $item) {
            $rawMaterial = $item->rawMaterial;
            $stockValidation[] = [
                'material' => $rawMaterial,
                'requested' => $item->requested_quantity,
                'available' => $rawMaterial->current_stock,
                'sufficient' => $rawMaterial->current_stock >= $item->requested_quantity
            ];
        }

        if ($request->ajax() || $request->query('modal') === '1') {
            return view('production-approvals.partials.detail', compact('productionRequest', 'stockValidation'));
        }

        // Fallback: redirect to index since the UI uses modal
        return redirect()->route('production-approvals.index');
    }

    /**
     * Approve the production request
     */
    public function approve(Request $request, ProductionRequest $productionRequest)
    {
        // Only allow approval of pending requests
        if (!$productionRequest->isPending()) {
            return redirect()->route('production-approvals.index')
                ->with('error', 'Hanya pengajuan dengan status "Menunggu Persetujuan" yang dapat disetujui.');
        }

        $request->validate([
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        try {
            $this->approveOne($productionRequest, $request->approval_notes);
        } catch (\Throwable $e) {
            return redirect()->route('production-approvals.index')->with('error', $e->getMessage());
        }

        return redirect()->route('production-approvals.index')
            ->with('success', 'Pengajuan produksi berhasil disetujui. Stok bahan mentah telah dikurangi dan produksi dapat dimulai.');
    }

    /**
     * Reject the production request
     */
    public function reject(Request $request, ProductionRequest $productionRequest)
    {
        // Only allow rejection of pending requests
        if (!$productionRequest->isPending()) {
            return redirect()->route('production-approvals.index')
                ->with('error', 'Hanya pengajuan dengan status "Menunggu Persetujuan" yang dapat ditolak.');
        }

        $request->validate([
            'approval_notes' => 'required|string|max:1000'
        ]);

        $this->rejectOne($productionRequest, $request->approval_notes);

        return redirect()->route('production-approvals.index')
            ->with('success', 'Pengajuan produksi berhasil ditolak.');
    }

    /**
     * Bulk approve pending production requests
     */
    public function bulkApprove(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:production_requests,id',
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        $success = 0; $failed = [];
        foreach ($data['ids'] as $id) {
            $pr = ProductionRequest::with(['items.rawMaterial'])->find($id);
            if (!$pr || !$pr->isPending()) { $failed[] = [$id, 'Status bukan pending']; continue; }
            try {
                $this->approveOne($pr, $data['approval_notes'] ?? null);
                $success++;
            } catch (\Throwable $e) {
                $failed[] = [$id, $e->getMessage()];
            }
        }

        $message = "Berhasil menyetujui {$success} pengajuan.";
        if (!empty($failed)) {
            $message .= " Gagal: " . count($failed) . " pengajuan.";
        }
        return redirect()->route('production-approvals.index')->with(['success' => $message, 'failed' => $failed]);
    }

    /**
     * Bulk reject pending production requests
     */
    public function bulkReject(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:production_requests,id',
            'approval_notes' => 'required|string|max:1000'
        ]);

        $success = 0; $failed = [];
        foreach ($data['ids'] as $id) {
            $pr = ProductionRequest::find($id);
            if (!$pr || !$pr->isPending()) { $failed[] = [$id, 'Status bukan pending']; continue; }
            try {
                $this->rejectOne($pr, $data['approval_notes']);
                $success++;
            } catch (\Throwable $e) {
                $failed[] = [$id, $e->getMessage()];
            }
        }

        $message = "Berhasil menolak {$success} pengajuan.";
        if (!empty($failed)) {
            $message .= " Gagal: " . count($failed) . " pengajuan.";
        }
        return redirect()->route('production-approvals.index')->with(['success' => $message, 'failed' => $failed]);
    }

    /**
     * Approve a single request with stock checks and updates (reusable)
     */
    private function approveOne(ProductionRequest $productionRequest, ?string $notes = null): void
    {
        DB::transaction(function () use ($productionRequest, $notes) {
            // Check stock availability for all items
            foreach ($productionRequest->items as $item) {
                $rawMaterial = $item->rawMaterial;
                if ($rawMaterial->current_stock < $item->requested_quantity) {
                    throw new \Exception("Stok tidak mencukupi untuk {$rawMaterial->name}. Tersedia: {$rawMaterial->current_stock}, Diminta: {$item->requested_quantity}");
                }
            }

            // Reduce raw material stock
            foreach ($productionRequest->items as $item) {
                $rawMaterial = $item->rawMaterial;
                $rawMaterial->decrement('current_stock', $item->requested_quantity);
            }

            // Update production request status
            $productionRequest->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $notes
            ]);
        });
    }

    /**
     * Reject a single request (reusable)
     */
    private function rejectOne(ProductionRequest $productionRequest, string $notes): void
    {
        $productionRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $notes
        ]);
    }
}
