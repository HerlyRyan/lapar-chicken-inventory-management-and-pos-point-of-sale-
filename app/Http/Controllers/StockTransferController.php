<?php

namespace App\Http\Controllers;

use App\Models\{Branch, StockMovement, StockTransfer, FinishedProduct, SemiFinishedProduct};
use App\Services\StockTransferService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Auth};
use Illuminate\Validation\Rule;

class StockTransferController extends Controller
{
    /**
     * Transfer stock between branches
     */
    public function transfer(Request $request, StockTransferService $stockTransferService)
    {
        $request->validate([
            'item_type' => 'required|in:finished,semi-finished',
            'item_id' => 'required|integer',
            'from_branch_id' => [
                'required',
                'integer',
                Rule::exists('branches', 'id')->where(fn($q) => $q->where('type', 'branch')),
            ],
            'to_branch_id' => [
                'required',
                'integer',
                Rule::exists('branches', 'id')->where(fn($q) => $q->where('type', 'branch')),
                'different:from_branch_id'
            ],
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $itemType = $request->item_type;
            $itemId = $request->item_id;
            $fromBranchId = $request->from_branch_id;
            $toBranchId = $request->to_branch_id;
            $quantity = $request->quantity;
            $notes = $request->notes;

            // Get branch names for logging
            $fromBranch = Branch::find($fromBranchId);
            $toBranch = Branch::find($toBranchId);

            if ($itemType === 'finished') {
                $stockTransferService->transferFinished((int)$itemId, (int)$fromBranchId, (int)$toBranchId, (float)$quantity, $notes, 'transfer', null);
            } else {
                $stockTransferService->transferSemiFinished((int)$itemId, (int)$fromBranchId, (int)$toBranchId, (float)$quantity, $notes, 'transfer', null);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => "Transfer berhasil dari {$fromBranch->name} ke {$toBranch->name}"
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Transfer gagal: ' . $e->getMessage()
            ], 400);
        }
    }


    /**
     * Display the stock transfer listing page
     */
    public function index(Request $request)
    {
        $query = StockTransfer::with(['fromBranch', 'toBranch', 'sentByUser', 'handledByUser', 'finishedProduct', 'semiFinishedProduct']);

        $columns = [
            ['key' => 'item_type', 'label' => 'Tipe Item'],
            ['key' => 'item_id', 'label' => 'Item'],
            ['key' => 'from_branch_id', 'label' => 'Dari Cabang'],
            ['key' => 'to_branch_id', 'label' => 'Ke Cabang'],
            ['key' => 'quantity', 'label' => 'Jumlah'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'notes', 'label' => 'Catatan'],
            ['key' => 'created_at', 'label' => 'Tanggal Pengiriman'],
            ['key' => 'handled_at', 'label' => 'Tanggal Penanganan'],
        ];

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('notes', 'like', $searchTerm)
                    ->orWhere('response_notes', 'like', $searchTerm);
            });
        }

        // Filter by from_branch
        if ($request->filled('from_branch_id') && !empty($request->from_branch_id)) {
            $query->where('from_branch_id', $request->from_branch_id);
        }

        // Filter by to_branch
        if ($request->filled('to_branch_id') && !empty($request->to_branch_id)) {
            $query->where('to_branch_id', $request->to_branch_id);
        }

        // Filter by item_type
        if ($request->filled('item_type') && $request->item_type != 'all') {
            $query->where('item_type', $request->item_type);
        }

        // Filter by status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date') && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // === SORTING ===
        if ($sortBy = $request->get('sort_by')) {
            $sortDir = $request->get('sort_dir', 'asc');

            switch ($sortBy) {
                case 'from_branch_id':
                    $query->leftJoin('branches as fb', 'fb.id', '=', 'stock_transfers.from_branch_id')
                        ->orderBy('fb.name', $sortDir)
                        ->select('stock_transfers.*');
                    break;

                case 'to_branch_id':
                    $query->leftJoin('branches as tb', 'tb.id', '=', 'stock_transfers.to_branch_id')
                        ->orderBy('tb.name', $sortDir)
                        ->select('stock_transfers.*');
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $transfers */
        $transfers = $query->paginate(10);

        // Get all branches for filter dropdown
        $branches = Branch::where('is_active', true)->orderBy('name')->pluck('name', 'id')->toArray();

        $itemTypes = [
            'finished' => 'Finished Product',
            'semi-finished' => 'Semi-Finished Product',
        ];

        $statuses = [
            'sent' => 'Dikirim',
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
        ];

        $selects = [
            ['name' => 'from_branch_id', 'label' => 'Dari Cabang', 'options' => $branches],
            ['name' => 'to_branch_id', 'label' => 'Ke Cabang', 'options' => $branches],
            ['name' => 'item_type', 'label' => 'Semua Tipe Item', 'options' => $itemTypes],
            ['name' => 'status', 'label' => 'Semua Status', 'options' => $statuses],
        ];

        // === RESPONSE ===
        if ($request->ajax()) {
            return response()->json([
                'data' => $transfers->items(),
                'links' => (string) $transfers->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('stock-transfer.index', [
            'transfers' => $transfers->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $transfers,
        ]);
    }

    /**
     * Show the form for creating a new transfer
     */
    public function create()
    {
        $currentBranchId = app()->bound('current_branch_id') ? app('current_branch_id') : (session('current_branch_id') ?? (Auth::user()->branch_id ?? null));
        $currentBranch = $currentBranchId ? Branch::find($currentBranchId) : null;

        // Hanya cabang yang boleh membuat transfer, bukan pusat produksi
        if ($currentBranch && $currentBranch->type !== 'branch') {
            return redirect()->route('stock-transfer.index')
                ->with('error', 'Hanya cabang yang dapat membuat transfer stok.');
        }

        $branches = Branch::retail()
            ->when($currentBranchId, function ($q) use ($currentBranchId) {
                return $q->where('id', '!=', $currentBranchId);
            })
            ->get();

        return view('stock-transfer.create', compact('branches', 'currentBranchId', 'currentBranch'));
    }

    /**
     * Store a new transfer (supports both single and batch)
     */
    public function store(Request $request, StockTransferService $stockTransferService)
    {
        // Support multi-item submissions: items[] = [{item_type,item_id,to_branch_id,quantity,notes}]
        $isBatch = $request->has('items');
        if ($isBatch) {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.item_type' => 'required|in:finished,semi-finished',
                'items.*.item_id' => 'required|integer',
                'items.*.to_branch_id' => [
                    'required',
                    'integer',
                    Rule::exists('branches', 'id')->where(fn($q) => $q->where('type', 'branch')),
                ],
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.notes' => 'nullable|string|max:255',
            ]);
        } else {
            $request->validate([
                'item_type' => 'required|in:finished,semi-finished',
                'item_id' => 'required|integer',
                'to_branch_id' => [
                    'required',
                    'integer',
                    Rule::exists('branches', 'id')->where(fn($q) => $q->where('type', 'branch')),
                ],
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string|max:255'
            ]);
        }

        $fromBranchId = app()->bound('current_branch_id') ? app('current_branch_id') : (session('current_branch_id') ?? (Auth::user()->branch_id ?? null));
        if (!$fromBranchId) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih cabang sumber di header terlebih dahulu.'
            ], 422);
        }

        // Pastikan sumber adalah cabang (bukan pusat produksi)
        $fromBranch = Branch::find($fromBranchId);
        if (!$fromBranch || $fromBranch->type !== 'branch') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya cabang yang dapat melakukan transfer stok.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $created = [];
            if ($isBatch) {
                foreach ($request->items as $item) {
                    $created[] = $stockTransferService->createPendingTransfer(
                        $item['item_type'],
                        (int) $item['item_id'],
                        (int) $fromBranchId,
                        (int) $item['to_branch_id'],
                        (int) $item['quantity'],
                        $item['notes'] ?? null
                    )->id;
                }
            } else {
                $created[] = $stockTransferService->createPendingTransfer(
                    $request->item_type,
                    (int) $request->item_id,
                    (int) $fromBranchId,
                    (int) $request->to_branch_id,
                    (int) $request->quantity,
                    $request->notes
                )->id;
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => $isBatch ? 'Transfer batch berhasil dibuat dan dikirim.' : 'Transfer berhasil dibuat dan dikirim.',
                'transfer_ids' => $created,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat transfer: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Inbox for destination branch to accept/reject transfers
     */
    public function inbox(Request $request)
    {
        $user = Auth::user();
        $branchId = null;
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin')) {
            $branchId = $request->input('branch_id', session('selected_branch_id'));
        } else {
            $branchId = $user?->branch_id;
        }

        if (!$branchId) {
            return redirect()->route('dashboard')
                ->with('error', 'Silakan pilih cabang terlebih dahulu untuk melihat kotak masuk transfer.');
        }

        $transfers = StockTransfer::with(['fromBranch', 'finishedProduct.unit', 'semiFinishedProduct.unit'])
            ->whereIn('status', ['sent', 'accepted', 'rejected'])
            ->where('to_branch_id', $branchId)
            ->orderByDesc('created_at')
            ->paginate(15);

        $branch = Branch::find($branchId);

        return view('stock-transfer.inbox', compact('transfers', 'branch', 'branchId'));
    }

    /**
     * Accept a pending transfer
     */
    public function accept(Request $request, StockTransferService $stockTransferService, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'sent') {
            return back()->with('error', 'Hanya transfer dengan status "Dikirim" yang dapat diterima.');
        }

        $user = Auth::user();
        $isSuperAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin');
        $sameBranch = $user?->branch_id === $stockTransfer->to_branch_id;
        $roleAllowed = true;
        if ($user && method_exists($user, 'hasRole')) {
            $roleAllowed = $user->hasRole('Kepala Toko') || $user->hasRole('Store Head') || $isSuperAdmin;
        }
        if (!($isSuperAdmin || ($sameBranch && $roleAllowed))) {
            return back()->with('error', 'Anda tidak berhak menerima transfer untuk cabang ini.');
        }

        $request->validate([
            'response_notes' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($request, $stockTransferService, $stockTransfer) {
            $stockTransferService->acceptTransfer($stockTransfer, $request->response_notes);
        });

        return back()->with('success', 'Transfer berhasil diterima. Stok telah ditambahkan.');
    }

    /**
     * Reject a pending transfer
     */
    public function reject(Request $request, StockTransferService $stockTransferService, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'sent') {
            return back()->with('error', 'Hanya transfer dengan status "Dikirim" yang dapat ditolak.');
        }

        $user = Auth::user();
        $isSuperAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('Super Admin');
        $sameBranch = $user?->branch_id === $stockTransfer->to_branch_id;
        $roleAllowed = true;
        if ($user && method_exists($user, 'hasRole')) {
            $roleAllowed = $user->hasRole('Kepala Toko') || $user->hasRole('Store Head') || $isSuperAdmin;
        }
        if (!($isSuperAdmin || ($sameBranch && $roleAllowed))) {
            return back()->with('error', 'Anda tidak berhak menolak transfer untuk cabang ini.');
        }

        $request->validate([
            'response_notes' => 'required|string|max:1000'
        ]);

        DB::transaction(function () use ($request, $stockTransferService, $stockTransfer) {
            $stockTransferService->rejectTransfer($stockTransfer, $request->response_notes);
        });

        return back()->with('success', 'Transfer ditolak. Stok dikembalikan ke cabang asal.');
    }

    /**
     * Show the form for editing a transfer
     */
    public function edit(StockTransfer $stockTransfer)
    {
        $currentBranchId = app()->bound('current_branch_id') ? app('current_branch_id') : (session('current_branch_id') ?? (Auth::user()->branch_id ?? null));

        // Check if user can edit this transfer
        if ($stockTransfer->from_branch_id !== $currentBranchId) {
            return redirect()->route('stock-transfer.index')
                ->with('error', 'Anda tidak dapat mengedit transfer dari cabang lain.');
        }

        $branches = Branch::retail()->where('id', '!=', $currentBranchId)->get();

        return view('stock-transfer.edit', compact('stockTransfer', 'branches'));
    }

    /**
     * Update a transfer (only pending transfers can be updated)
     */
    public function update(Request $request, StockTransfer $stockTransfer, StockTransferService $stockTransferService)
    {
        if ($stockTransfer->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya transfer dengan status pending yang dapat diedit.'
            ], 422);
        }

        $request->validate([
            'item_type' => 'required|in:finished,semi-finished',
            'item_id' => 'required|integer',
            'to_branch_id' => [
                'required',
                'integer',
                Rule::exists('branches', 'id')->where(fn($q) => $q->where('type', 'branch')),
            ],
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            $stockTransfer->update([
                'item_type' => $request->item_type,
                'item_id' => $request->item_id,
                'to_branch_id' => $request->to_branch_id,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
            ]);

            // Re-send the transfer
            $stockTransferService->sendTransfer($stockTransfer);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transfer berhasil diperbarui dan dikirim ulang.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Cancel a transfer
     */
    public function cancel(StockTransfer $stockTransfer, StockTransferService $stockTransferService)
    {
        if ($stockTransfer->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya transfer dengan status pending yang dapat dibatalkan.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $stockTransferService->cancelTransfer($stockTransfer);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Transfer berhasil dibatalkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan transfer: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get transfer detail for modal
     */
    public function detail(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['fromBranch', 'toBranch', 'finishedProduct.unit', 'semiFinishedProduct.unit', 'sentByUser', 'handledByUser']);

        return view('stock-transfer.partials.detail', compact('stockTransfer'));
    }

    /**
     * Legacy method - redirect to store
     */
    public function request(Request $request, StockTransferService $stockTransferService)
    {
        return $this->store($request, $stockTransferService);
    }
}
