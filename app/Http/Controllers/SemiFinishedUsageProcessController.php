<?php

namespace App\Http\Controllers;

use App\Models\FinishedProduct;
use App\Models\SemiFinishedUsageRequest;
use App\Models\SemiFinishedUsageRequestOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SemiFinishedUsageProcessController extends Controller
{
    /**
     * Restrict to retail branches only (reuse behavior from request controller)
     */
    private function assertRetailBranchOnly(): void
    {
        $currentBranch = app()->bound('current_branch') ? app('current_branch') : null;
        if ($currentBranch && $currentBranch->type === 'production') {
            abort(403, 'Fitur ini hanya tersedia di toko cabang, bukan pusat produksi.');
        }
    }

    /**
     * List usage requests that are ready for processing or in-progress for the current branch
     */
    public function index(Request $request)
    {
        $this->assertRetailBranchOnly();

        $branchId = app()->bound('current_branch_id') ? app('current_branch_id') : (Auth::user()->branch_id ?? null);

        $statusFilter = $request->input('status', 'all');
        $query = SemiFinishedUsageRequest::query()
            ->with(['requestingBranch', 'requestedBy'])
            ->where('requesting_branch_id', $branchId);

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        } else {
            // Default to show approved or processing
            $query->whereIn('status', [
                SemiFinishedUsageRequest::STATUS_APPROVED,
                SemiFinishedUsageRequest::STATUS_PROCESSING,
            ]);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('semi-finished-usage-processes.index', compact('requests', 'statusFilter'));
    }

    /**
     * Show a specific request processing page
     */
    public function show(SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $this->assertRetailBranchOnly();

        // Scope to branch
        $branchId = app()->bound('current_branch_id') ? app('current_branch_id') : (Auth::user()->branch_id ?? null);
        if ($semiFinishedUsageRequest->requesting_branch_id !== $branchId) {
            abort(403, 'Anda tidak dapat memproses permintaan dari cabang lain.');
        }

        $semiFinishedUsageRequest->load([
            'requestingBranch',
            'requestedBy',
            'outputs.finishedProduct.unit',
        ]);

        return view('semi-finished-usage-processes.show', [
            'usageRequest' => $semiFinishedUsageRequest,
        ]);
    }

    /**
     * Mark processing started
     */
    public function start(Request $request, SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $this->assertRetailBranchOnly();
        if ($semiFinishedUsageRequest->status !== SemiFinishedUsageRequest::STATUS_APPROVED) {
            return back()->with('error', 'Hanya permintaan yang sudah disetujui yang dapat dimulai prosesnya.');
        }

        $branchId = app()->bound('current_branch_id') ? app('current_branch_id') : (Auth::user()->branch_id ?? null);
        if ($semiFinishedUsageRequest->requesting_branch_id !== $branchId) {
            abort(403, 'Anda tidak dapat memproses permintaan dari cabang lain.');
        }

        $semiFinishedUsageRequest->update(['status' => SemiFinishedUsageRequest::STATUS_PROCESSING]);

        return redirect()->route('semi-finished-usage-processes.show', $semiFinishedUsageRequest)
            ->with('success', 'Proses telah dimulai.');
    }

    /**
     * Update actual outputs and optional notes while processing
     */
    public function updateStatus(Request $request, SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $this->assertRetailBranchOnly();
        if (!in_array($semiFinishedUsageRequest->status, [
            SemiFinishedUsageRequest::STATUS_APPROVED,
            SemiFinishedUsageRequest::STATUS_PROCESSING,
        ])) {
            return back()->with('error', 'Permintaan ini tidak dapat diperbarui pada status saat ini.');
        }

        $validator = Validator::make($request->all(), [
            'outputs' => 'nullable|array',
            'outputs.*.id' => 'required|exists:semi_finished_usage_request_outputs,id',
            'outputs.*.actual_quantity' => 'nullable|integer|min:0',
            'outputs.*.notes' => 'nullable|string|max:1000',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            if ($request->filled('outputs')) {
                foreach ($request->outputs as $o) {
                    /** @var SemiFinishedUsageRequestOutput $output */
                    $output = SemiFinishedUsageRequestOutput::where('semi_finished_request_id', $semiFinishedUsageRequest->id)
                        ->where('id', $o['id'])
                        ->first();
                    if ($output) {
                        $payload = [
                            'notes' => $o['notes'] ?? $output->notes,
                        ];
                        if (array_key_exists('actual_quantity', $o)) {
                            $payload['actual_quantity'] = $o['actual_quantity'];
                        }
                        $output->update($payload);
                    }
                }
            }

            // If still approved, move to processing on first update
            if ($semiFinishedUsageRequest->status === SemiFinishedUsageRequest::STATUS_APPROVED) {
                $semiFinishedUsageRequest->update(['status' => SemiFinishedUsageRequest::STATUS_PROCESSING]);
            }

            DB::commit();

            return redirect()->route('semi-finished-usage-processes.show', $semiFinishedUsageRequest)
                ->with('success', 'Data proses berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Complete processing and update finished product stock for the branch
     */
    public function complete(Request $request, SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $this->assertRetailBranchOnly();
        if ($semiFinishedUsageRequest->status !== SemiFinishedUsageRequest::STATUS_PROCESSING) {
            return back()->with('error', 'Hanya permintaan yang sedang diproses yang dapat diselesaikan.');
        }

        $branchId = $semiFinishedUsageRequest->requesting_branch_id;

        // Accept optional latest outputs payload on completion
        $validator = Validator::make($request->all(), [
            'outputs' => 'nullable|array',
            'outputs.*.id' => 'required|exists:semi_finished_usage_request_outputs,id',
            'outputs.*.actual_quantity' => 'nullable|integer|min:0',
            'outputs.*.notes' => 'nullable|string|max:1000',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Update outputs if provided from the completion form
            if ($request->filled('outputs')) {
                foreach ($request->outputs as $o) {
                    /** @var SemiFinishedUsageRequestOutput|null $output */
                    $output = SemiFinishedUsageRequestOutput::where('semi_finished_request_id', $semiFinishedUsageRequest->id)
                        ->where('id', $o['id'])
                        ->first();
                    if ($output) {
                        $payload = [
                            'notes' => $o['notes'] ?? $output->notes,
                        ];
                        if (array_key_exists('actual_quantity', $o)) {
                            $payload['actual_quantity'] = $o['actual_quantity'];
                        }
                        $output->update($payload);
                    }
                }
            }

            // Ensure outputs are loaded with latest values
            $semiFinishedUsageRequest->load('outputs.finishedProduct');

            // Add finished stock per actual output
            foreach ($semiFinishedUsageRequest->outputs as $output) {
                $actual = (int) ($output->actual_quantity ?? 0);
                if ($actual > 0 && $output->finishedProduct) {
                    /** @var FinishedProduct $fp */
                    $fp = $output->finishedProduct;
                    $notes = 'Hasil produksi dari SFR #' . $semiFinishedUsageRequest->request_number;
                    $fp->updateStockForBranch($branchId, 'in', $actual, $notes, Auth::id());
                }
            }

            // Finalize request status
            $semiFinishedUsageRequest->update(['status' => SemiFinishedUsageRequest::STATUS_COMPLETED]);

            DB::commit();

            return redirect()->route('semi-finished-usage-processes.show', $semiFinishedUsageRequest)
                ->with('success', 'Proses selesai. Stok produk jadi telah diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyelesaikan proses: ' . $e->getMessage());
        }
    }
}
