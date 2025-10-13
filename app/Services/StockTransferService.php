<?php

namespace App\Services;

use App\Models\{Branch, FinishedBranchStock, SemiFinishedBranchStock, StockMovement, StockTransfer};
use Illuminate\Support\Facades\Auth;
use Exception;

class StockTransferService
{
    /**
     * Transfer semi-finished product between branches and record movements.
     * Caller should wrap in a DB transaction if atomicity across operations is required.
     */
    public function transferSemiFinished(int $itemId, int $fromBranchId, int $toBranchId, float $quantity, ?string $notes = null, ?string $referenceType = null, $referenceId = null): void
    {
        if ($fromBranchId === $toBranchId) {
            throw new Exception('Cabang asal dan tujuan tidak boleh sama');
        }

        // Check and deduct from source
        $fromStock = SemiFinishedBranchStock::where('branch_id', $fromBranchId)
            ->where('semi_finished_product_id', $itemId)
            ->lockForUpdate()
            ->first();

        if (!$fromStock || $fromStock->quantity < $quantity) {
            throw new Exception('Stok tidak mencukupi di cabang asal');
        }

        $fromStock->quantity -= $quantity;
        $fromStock->save();

        // Add to destination
        $toStock = SemiFinishedBranchStock::firstOrCreate([
            'branch_id' => $toBranchId,
            'semi_finished_product_id' => $itemId,
        ], [
            'quantity' => 0,
        ]);

        // Lock destination row before update to avoid race conditions
        $toStock->refresh();
        $toStock->quantity += $quantity;
        $toStock->save();

        $fromBranch = Branch::find($fromBranchId);
        $toBranch = Branch::find($toBranchId);

        // Movements
        StockMovement::create([
            'item_type' => 'semi_finished_product',
            'item_id' => $itemId,
            'branch_id' => $fromBranchId,
            'type' => 'transfer_out',
            'quantity' => $quantity,
            'reference_type' => $referenceType ?? 'transfer',
            'reference_id' => $referenceId,
            'notes' => 'Transfer ke ' . ($toBranch->name ?? 'Unknown') . ($notes ? ' - ' . $notes : ''),
            'created_by' => Auth::id(),
        ]);

        StockMovement::create([
            'item_type' => 'semi_finished_product',
            'item_id' => $itemId,
            'branch_id' => $toBranchId,
            'type' => 'transfer_in',
            'quantity' => $quantity,
            'reference_type' => $referenceType ?? 'transfer',
            'reference_id' => $referenceId,
            'notes' => 'Transfer dari ' . ($fromBranch->name ?? 'Unknown') . ($notes ? ' - ' . $notes : ''),
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Transfer finished product between branches and record movements.
     */
    public function transferFinished(int $itemId, int $fromBranchId, int $toBranchId, float $quantity, ?string $notes = null, ?string $referenceType = null, $referenceId = null): void
    {
        if ($fromBranchId === $toBranchId) {
            throw new Exception('Cabang asal dan tujuan tidak boleh sama');
        }

        $fromStock = FinishedBranchStock::where('branch_id', $fromBranchId)
            ->where('finished_product_id', $itemId)
            ->lockForUpdate()
            ->first();

        if (!$fromStock || $fromStock->quantity < $quantity) {
            throw new Exception('Stok tidak mencukupi di cabang asal');
        }

        $fromStock->quantity -= $quantity;
        $fromStock->save();

        $toStock = FinishedBranchStock::firstOrCreate([
            'branch_id' => $toBranchId,
            'finished_product_id' => $itemId,
        ], [
            'quantity' => 0,
        ]);

        $toStock->refresh();
        $toStock->quantity += $quantity;
        $toStock->save();

        $fromBranch = Branch::find($fromBranchId);
        $toBranch = Branch::find($toBranchId);

        StockMovement::create([
            'item_type' => 'finished_product',
            'item_id' => $itemId,
            'branch_id' => $fromBranchId,
            'type' => 'transfer_out',
            'quantity' => $quantity,
            'reference_type' => $referenceType ?? 'transfer',
            'reference_id' => $referenceId,
            'notes' => 'Transfer ke ' . ($toBranch->name ?? 'Unknown') . ($notes ? ' - ' . $notes : ''),
            'created_by' => Auth::id(),
        ]);

        StockMovement::create([
            'item_type' => 'finished_product',
            'item_id' => $itemId,
            'branch_id' => $toBranchId,
            'type' => 'transfer_in',
            'quantity' => $quantity,
            'reference_type' => $referenceType ?? 'transfer',
            'reference_id' => $referenceId,
            'notes' => 'Transfer dari ' . ($fromBranch->name ?? 'Unknown') . ($notes ? ' - ' . $notes : ''),
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Create a pending transfer (two-step workflow):
     * - Immediately deduct from source branch
     * - Do NOT add to destination yet
     * - Record StockMovement transfer_out
     * - Persist StockTransfer row with status=sent
     */
    public function createPendingTransfer(string $itemType, int $itemId, int $fromBranchId, int $toBranchId, int $quantity, ?string $notes = null): StockTransfer
    {
        if ($fromBranchId === $toBranchId) {
            throw new Exception('Cabang asal dan tujuan tidak boleh sama');
        }

        if (!in_array($itemType, ['finished', 'semi-finished'], true)) {
            throw new Exception('Tipe item tidak valid');
        }

        // Deduct from source by item type
        if ($itemType === 'finished') {
            $fromStock = FinishedBranchStock::where('branch_id', $fromBranchId)
                ->where('finished_product_id', $itemId)
                ->lockForUpdate()
                ->first();
            if (!$fromStock || $fromStock->quantity < $quantity) {
                throw new Exception('Stok tidak mencukupi di cabang asal');
            }
            $fromStock->quantity -= $quantity;
            $fromStock->save();
        } else {
            $fromStock = SemiFinishedBranchStock::where('branch_id', $fromBranchId)
                ->where('semi_finished_product_id', $itemId)
                ->lockForUpdate()
                ->first();
            if (!$fromStock || $fromStock->quantity < $quantity) {
                throw new Exception('Stok tidak mencukupi di cabang asal');
            }
            $fromStock->quantity -= $quantity;
            $fromStock->save();
        }

        $fromBranch = Branch::find($fromBranchId);
        $toBranch = Branch::find($toBranchId);

        // Log movement: transfer_out at source
        StockMovement::create([
            'item_type' => $itemType === 'finished' ? 'finished_product' : 'semi_finished_product',
            'item_id' => $itemId,
            'branch_id' => $fromBranchId,
            'type' => 'transfer_out',
            'quantity' => $quantity,
            'reference_type' => 'stock_transfer',
            'reference_id' => null, // will be set conceptually to transfer id (not required for now)
            'notes' => 'Transfer ke ' . ($toBranch->name ?? 'Unknown') . ($notes ? ' - ' . $notes : ''),
            'created_by' => Auth::id(),
        ]);

        // Persist transfer row
        $transfer = StockTransfer::create([
            'item_type' => $itemType,
            'item_id' => $itemId,
            'from_branch_id' => $fromBranchId,
            'to_branch_id' => $toBranchId,
            'quantity' => $quantity,
            'notes' => $notes,
            'status' => 'sent',
            'sent_by' => Auth::id(),
        ]);

        return $transfer;
    }

    /**
     * Accept a pending transfer: add to destination, mark accepted, and log transfer_in
     */
    public function acceptTransfer(StockTransfer $transfer, ?string $responseNotes = null): void
    {
        if ($transfer->status !== 'sent') {
            throw new Exception('Hanya transfer berstatus "Dikirim" yang dapat diterima');
        }

        // Add to destination stock
        if ($transfer->item_type === 'finished') {
            $toStock = FinishedBranchStock::firstOrCreate([
                'branch_id' => $transfer->to_branch_id,
                'finished_product_id' => $transfer->item_id,
            ], [ 'quantity' => 0 ]);
            $toStock->refresh();
            $toStock->quantity += (float) $transfer->quantity;
            $toStock->save();
        } else {
            $toStock = SemiFinishedBranchStock::firstOrCreate([
                'branch_id' => $transfer->to_branch_id,
                'semi_finished_product_id' => $transfer->item_id,
            ], [ 'quantity' => 0 ]);
            $toStock->refresh();
            $toStock->quantity += (float) $transfer->quantity;
            $toStock->save();
        }

        $fromBranch = Branch::find($transfer->from_branch_id);
        $toBranch = Branch::find($transfer->to_branch_id);

        // Log movement: transfer_in at destination
        StockMovement::create([
            'item_type' => $transfer->item_type === 'finished' ? 'finished_product' : 'semi_finished_product',
            'item_id' => $transfer->item_id,
            'branch_id' => $transfer->to_branch_id,
            'type' => 'transfer_in',
            'quantity' => (float) $transfer->quantity,
            'reference_type' => 'stock_transfer',
            'reference_id' => $transfer->id,
            'notes' => 'Transfer dari ' . ($fromBranch->name ?? 'Unknown') . ($responseNotes ? ' - ' . $responseNotes : ''),
            'created_by' => Auth::id(),
        ]);

        // Mark accepted
        $transfer->update([
            'status' => 'accepted',
            'handled_by' => Auth::id(),
            'handled_at' => now(),
            'response_notes' => $responseNotes,
        ]);
    }

    /**
     * Reject a pending transfer: revert stock to source, mark rejected
     */
    public function rejectTransfer(StockTransfer $transfer, ?string $responseNotes = null): void
    {
        if ($transfer->status !== 'sent') {
            throw new Exception('Hanya transfer berstatus "Dikirim" yang dapat ditolak');
        }

        // Return stock to source
        if ($transfer->item_type === 'finished') {
            $fromStock = FinishedBranchStock::firstOrCreate([
                'branch_id' => $transfer->from_branch_id,
                'finished_product_id' => $transfer->item_id,
            ], [ 'quantity' => 0 ]);
            $fromStock->refresh();
            $fromStock->quantity += (float) $transfer->quantity;
            $fromStock->save();
        } else {
            $fromStock = SemiFinishedBranchStock::firstOrCreate([
                'branch_id' => $transfer->from_branch_id,
                'semi_finished_product_id' => $transfer->item_id,
            ], [ 'quantity' => 0 ]);
            $fromStock->refresh();
            $fromStock->quantity += (float) $transfer->quantity;
            $fromStock->save();
        }

        // Optionally log a revert movement at source (as transfer_in)
        StockMovement::create([
            'item_type' => $transfer->item_type === 'finished' ? 'finished_product' : 'semi_finished_product',
            'item_id' => $transfer->item_id,
            'branch_id' => $transfer->from_branch_id,
            'type' => 'transfer_in',
            'quantity' => (float) $transfer->quantity,
            'reference_type' => 'stock_transfer',
            'reference_id' => $transfer->id,
            'notes' => 'Pengembalian transfer (ditolak) dari cabang tujuan' . ($responseNotes ? ' - ' . $responseNotes : ''),
            'created_by' => Auth::id(),
        ]);

        // Mark rejected
        $transfer->update([
            'status' => 'rejected',
            'handled_by' => Auth::id(),
            'handled_at' => now(),
            'response_notes' => $responseNotes,
        ]);
    }

    /**
     * Send/resend a transfer (change status from pending to sent)
     */
    public function sendTransfer(StockTransfer $transfer): void
    {
        if ($transfer->status !== 'pending') {
            throw new Exception('Hanya transfer dengan status pending yang dapat dikirim');
        }

        $transfer->update([
            'status' => 'sent',
            'sent_by' => Auth::id(),
        ]);
    }

    /**
     * Cancel a pending transfer and return stock to source
     */
    public function cancelTransfer(StockTransfer $transfer): void
    {
        if ($transfer->status !== 'pending') {
            throw new Exception('Hanya transfer dengan status pending yang dapat dibatalkan');
        }

        // Return stock to source
        if ($transfer->item_type === 'finished') {
            $fromStock = FinishedBranchStock::firstOrCreate([
                'branch_id' => $transfer->from_branch_id,
                'finished_product_id' => $transfer->item_id,
            ], [ 'quantity' => 0 ]);
            $fromStock->refresh();
            $fromStock->quantity += (float) $transfer->quantity;
            $fromStock->save();
        } else {
            $fromStock = SemiFinishedBranchStock::firstOrCreate([
                'branch_id' => $transfer->from_branch_id,
                'semi_finished_product_id' => $transfer->item_id,
            ], [ 'quantity' => 0 ]);
            $fromStock->refresh();
            $fromStock->quantity += (float) $transfer->quantity;
            $fromStock->save();
        }

        // Log revert movement at source
        StockMovement::create([
            'item_type' => $transfer->item_type === 'finished' ? 'finished_product' : 'semi_finished_product',
            'item_id' => $transfer->item_id,
            'branch_id' => $transfer->from_branch_id,
            'type' => 'transfer_in',
            'quantity' => (float) $transfer->quantity,
            'reference_type' => 'stock_transfer',
            'reference_id' => $transfer->id,
            'notes' => 'Pengembalian transfer (dibatalkan)',
            'created_by' => Auth::id(),
        ]);

        // Mark cancelled
        $transfer->update([
            'status' => 'cancelled',
            'handled_by' => Auth::id(),
            'handled_at' => now(),
        ]);
    }
}
