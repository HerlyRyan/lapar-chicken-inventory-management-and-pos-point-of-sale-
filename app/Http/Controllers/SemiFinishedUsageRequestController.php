<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SemiFinishedUsageRequest;
use App\Models\SemiFinishedUsageRequestItem;
use App\Models\SemiFinishedProduct;
use App\Models\FinishedProduct;
use App\Models\SemiFinishedUsageRequestOutput;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class SemiFinishedUsageRequestController extends Controller
{
    // Ensure this feature is only accessible from retail branches (not production centers)
    private function assertRetailBranchOnly(): void
    {
        $currentBranch = app()->bound('current_branch') ? app('current_branch') : null;
        if ($currentBranch && $currentBranch->type === 'production') {
            abort(403, 'Fitur ini hanya tersedia di toko cabang, bukan pusat produksi.');
        }
    }

    /**
     * Display a listing of the material usage requests
     */
    public function index(Request $request)
    {
        $query = SemiFinishedUsageRequest::query()
            ->with(['requestingBranch', 'requestedBy', 'approvedByUser']);

        $columns = [
            ['key' => 'request_number', 'label' => 'Nomor Permintaan'],
            ['key' => 'branch', 'label' => 'Cabang'],
            ['key' => 'purpose', 'label' => 'Tujuan'],
            ['key' => 'requested_date', 'label' => 'Tanggal Permintaan'],
            ['key' => 'required_date', 'label' => 'Tanggal Dibutuhkan'],
            ['key' => 'status', 'label' => 'Status'],
        ];

        // Filter by status
        if (!empty($request->status) && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by branch for admin users
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin');
        $isAdmin = $user->hasRole('admin');

        // Only allow access from retail branches
        $this->assertRetailBranchOnly();

        if (!$isSuperAdmin && !$isAdmin) {
            // Regular branch users can only see their branch's requests
            $branchId = app()->bound('current_branch_id') ? app('current_branch_id') : ($user->branch_id ?? null);
            if ($branchId) {
                $query->where('requesting_branch_id', $branchId);
            }
        } else if ($request->has('branch_id') && $request->branch_id != 'all') {
            // Admin filtering by branch
            $query->where('requesting_branch_id', $request->branch_id);
        }

        // Get branches for filter
        $branches = Branch::orderBy('name')->get();

        /** @var \Illuminate\Pagination\LengthAwarePaginator $requests */
        $requests = $query->orderBy('created_at', 'desc')->paginate(10);

        $statuses = [
            'pending' => 'Ditunda',
            'completed' => 'Diterima',
            'rejected' => 'Ditolak',
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
                'data' => $requests->items(),
                'links' => (string) $requests->links('vendor.pagination.tailwind'),
            ]);
        }

        $viewName = View::exists('semi-finished-usage-requests.index')
            ? 'semi-finished-usage-requests.index'
            : 'material-usage-requests.index';

        return view($viewName, compact('requests', 'branches', 'columns', 'selects'));
    }

    /**
     * Show the form for creating a new material usage request
     */
    public function create()
    {
        $this->assertRetailBranchOnly();
        // Default requesting branch from current context
        $branchId = app()->bound('current_branch_id') ? app('current_branch_id') : (Auth::user()->branch_id ?? null);
        if (!$branchId) {
            return redirect()->route('dashboard')->with('error', 'Silakan pilih cabang aktif terlebih dahulu untuk membuat permintaan penggunaan bahan.');
        }
        $requestingBranch = Branch::findOrFail($branchId);

        // Get all active semi-finished products for selection (filtered by branch if available)
        $semiFinishedProducts = SemiFinishedProduct::active()
            ->forBranch($branchId)
            ->with('unit')
            ->orderBy('name')
            ->get();
        $units = Unit::orderBy('unit_name')->get();
        // Finished products for target stock section
        $finishedProducts = FinishedProduct::active()
            ->with('unit')
            ->orderBy('name')
            ->get();

        $viewName = View::exists('semi-finished-usage-requests.create')
            ? 'semi-finished-usage-requests.create'
            : 'material-usage-requests.create';
        return view($viewName, compact(
            'semiFinishedProducts',
            'units',
            'requestingBranch',
            'finishedProducts'
        ));
    }

    /**
     * Store a newly created semi-finished usage request
     */
    public function store(Request $request)
    {
        $this->assertRetailBranchOnly();
        // Backward compatibility: map legacy 'targets' payload to new 'outputs'
        if (!$request->filled('outputs') && $request->filled('targets')) {
            $request->merge(['outputs' => $request->input('targets')]);
        }
        // Normalize outputs keys: prefer product_id, accept finished_product_id
        if ($request->filled('outputs') && is_array($request->outputs)) {
            $normalized = [];
            foreach ($request->outputs as $o) {
                if (is_array($o)) {
                    if (!isset($o['product_id']) && isset($o['finished_product_id'])) {
                        $o['product_id'] = $o['finished_product_id'];
                    }
                    $normalized[] = $o;
                }
            }
            $request->merge(['outputs' => $normalized]);
        }
        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|max:255',
            'required_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            // Keep field name raw_material_id for compatibility, but validate against semi_finished_products
            'items.*.raw_material_id' => 'required|exists:semi_finished_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.notes' => 'nullable|string',
            // Outputs (optional - replaces targets)
            'outputs' => 'nullable|array',
            'outputs.*.product_id' => 'required_with:outputs|exists:finished_products,id',
            'outputs.*.planned_quantity' => 'required_with:outputs|integer|min:1',
            'outputs.*.notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Optional validation for approval note (matches UI maxlength)
        $validator = Validator::make($request->all(), [
            'approval_note' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create the semi-finished usage request
            $branchId = app()->bound('current_branch_id') ? app('current_branch_id') : (Auth::user()->branch_id ?? null);
            $semiFinishedRequest = SemiFinishedUsageRequest::create([
                'request_number' => SemiFinishedUsageRequest::generateRequestNumber(),
                'requesting_branch_id' => $branchId,
                'user_id' => Auth::id(),
                'status' => SemiFinishedUsageRequest::STATUS_PENDING,
                'requested_date' => now()->toDateString(),
                'required_date' => $request->required_date,
                'purpose' => $request->purpose,
                'notes' => $request->notes,
            ]);

            // Add items to the request (using SemiFinishedProduct)
            foreach ($request->items as $itemData) {
                $product = SemiFinishedProduct::findOrFail($itemData['raw_material_id']);

                // Build payload with primary column; add legacy raw_material_id only if it exists
                $payload = [
                    'semi_finished_request_id' => $semiFinishedRequest->id,
                    'semi_finished_product_id' => $itemData['raw_material_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_id' => $itemData['unit_id'],
                    'unit_price' => $product->unit_price ?? 0.00, // Store current price; fallback to 0.00
                    'notes' => $itemData['notes'] ?? null,
                ];
                if (Schema::hasColumn('semi_finished_usage_request_items', 'raw_material_id')) {
                    $payload['raw_material_id'] = $itemData['raw_material_id'];
                }

                SemiFinishedUsageRequestItem::create($payload);
            }

            // Save planned outputs if provided (new schema)
            if ($request->filled('outputs')) {
                foreach ($request->outputs as $output) {
                    $productId = $output['product_id'] ?? null;
                    if (!empty($productId) && !empty($output['planned_quantity'])) {
                        SemiFinishedUsageRequestOutput::create([
                            'semi_finished_request_id' => $semiFinishedRequest->id,
                            // Use correct column name per schema
                            'product_id' => $productId,
                            'planned_quantity' => $output['planned_quantity'],
                            'actual_quantity' => $output['actual_quantity'] ?? null,
                            'notes' => $output['notes'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            // Redirect to index (listing) with branch context after creation
            return redirect()
                ->route('semi-finished-usage-requests.index', ['branch_id' => $branchId])
                ->with('success', 'Permintaan penggunaan bahan berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified material usage request
     */
    public function show(SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $semiFinishedUsageRequest->load([
            'requestingBranch',
            'requestedBy',
            'approvedByUser',
            // Aliases used in some views
            'user',
            'approvalUser',
            'items.semiFinishedProduct',
            'items.unit'
        ]);

        $viewName = View::exists('semi-finished-usage-requests.show')
            ? 'semi-finished-usage-requests.show'
            : 'material-usage-requests.show';
        // Keep view variable name for backward compatibility
        return view($viewName, ['materialUsageRequest' => $semiFinishedUsageRequest]);
    }

    /**
     * Show the form for editing the specified material usage request
     */
    public function edit(SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $this->assertRetailBranchOnly();
        // Can only edit if still pending
        if ($semiFinishedUsageRequest->status != SemiFinishedUsageRequest::STATUS_PENDING) {
            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('error', 'Hanya permintaan dengan status menunggu persetujuan yang dapat diubah.');
        }

        // Load relations
        $semiFinishedUsageRequest->load([
            'requestingBranch',
            'items.semiFinishedProduct',
            'items.unit',
            // Keep targets for backward compatibility in views, but also load outputs for new logic
            'targets',
            'outputs'
        ]);

        // Get all active semi-finished products for selection
        $semiFinishedProducts = SemiFinishedProduct::active()
            ->forBranch($semiFinishedUsageRequest->requesting_branch_id)
            ->with('unit')
            ->orderBy('name')
            ->get();
        $units = Unit::orderBy('unit_name')->get();
        // Finished products for target stock section
        $finishedProducts = FinishedProduct::active()
            ->with('unit')
            ->orderBy('name')
            ->get();

        $viewName = View::exists('semi-finished-usage-requests.edit')
            ? 'semi-finished-usage-requests.edit'
            : 'material-usage-requests.edit';
        // Keep view variable name for backward compatibility
        return view($viewName, [
            'materialUsageRequest' => $semiFinishedUsageRequest,
            'semiFinishedProducts' => $semiFinishedProducts,
            'units' => $units,
            'finishedProducts' => $finishedProducts,
        ]);
    }

    /**
     * Update the specified material usage request
     */
    public function update(Request $request, SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $this->assertRetailBranchOnly();
        // Can only update if still pending
        if ($semiFinishedUsageRequest->status != SemiFinishedUsageRequest::STATUS_PENDING) {
            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('error', 'Hanya permintaan dengan status menunggu persetujuan yang dapat diubah.');
        }

        // Backward compatibility: map legacy 'targets' payload to new 'outputs'
        if (!$request->filled('outputs') && $request->filled('targets')) {
            $request->merge(['outputs' => $request->input('targets')]);
        }
        // Normalize outputs keys: prefer product_id, accept finished_product_id
        if ($request->filled('outputs') && is_array($request->outputs)) {
            $normalized = [];
            foreach ($request->outputs as $o) {
                if (is_array($o)) {
                    if (!isset($o['product_id']) && isset($o['finished_product_id'])) {
                        $o['product_id'] = $o['finished_product_id'];
                    }
                    $normalized[] = $o;
                }
            }
            $request->merge(['outputs' => $normalized]);
        }

        $validator = Validator::make($request->all(), [
            'purpose' => 'required|string|max:255',
            'required_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:semi_finished_usage_request_items,id',
            // Validate against semi_finished_products
            'items.*.raw_material_id' => 'required|exists:semi_finished_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.notes' => 'nullable|string',
            // Outputs (optional - replaces targets)
            'outputs' => 'nullable|array',
            'outputs.*.id' => 'nullable|integer',
            'outputs.*.product_id' => 'required_with:outputs|exists:finished_products,id',
            'outputs.*.planned_quantity' => 'required_with:outputs|integer|min:1',
            'outputs.*.actual_quantity' => 'nullable|integer|min:0',
            'outputs.*.notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update the semi-finished usage request
            $semiFinishedUsageRequest->update([
                'required_date' => $request->required_date,
                'purpose' => $request->purpose,
                'notes' => $request->notes,
            ]);

            // Get existing item IDs
            $existingItemIds = $semiFinishedUsageRequest->items->pluck('id')->toArray();
            $updatedItemIds = [];

            // Update or create items (using SemiFinishedProduct)
            foreach ($request->items as $itemData) {
                $product = SemiFinishedProduct::findOrFail($itemData['raw_material_id']);

                if (isset($itemData['id'])) {
                    // Update existing item
                    $item = SemiFinishedUsageRequestItem::find($itemData['id']);
                    $payload = [
                        'semi_finished_product_id' => $itemData['raw_material_id'],
                        'quantity' => $itemData['quantity'],
                        'unit_id' => $itemData['unit_id'],
                        'unit_price' => $product->unit_price ?? 0.00,
                        'notes' => $itemData['notes'] ?? null,
                    ];
                    if (Schema::hasColumn('semi_finished_usage_request_items', 'raw_material_id')) {
                        $payload['raw_material_id'] = $itemData['raw_material_id'];
                    }
                    $item->update($payload);
                    $updatedItemIds[] = $item->id;
                } else {
                    // Create new item
                    $payload = [
                        'semi_finished_request_id' => $semiFinishedUsageRequest->id,
                        'semi_finished_product_id' => $itemData['raw_material_id'],
                        'quantity' => $itemData['quantity'],
                        'unit_id' => $itemData['unit_id'],
                        'unit_price' => $product->unit_price ?? 0.00,
                        'notes' => $itemData['notes'] ?? null,
                    ];
                    if (Schema::hasColumn('semi_finished_usage_request_items', 'raw_material_id')) {
                        $payload['raw_material_id'] = $itemData['raw_material_id'];
                    }
                    $item = SemiFinishedUsageRequestItem::create($payload);
                    $updatedItemIds[] = $item->id;
                }
            }

            // Delete removed items
            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            if (count($itemsToDelete) > 0) {
                SemiFinishedUsageRequestItem::whereIn('id', $itemsToDelete)->delete();
            }

            // ================= Outputs Upsert/Delete (replaces Targets) =================
            $existingOutputIds = $semiFinishedUsageRequest->outputs()->pluck('id')->toArray();
            $updatedOutputIds = [];
            if ($request->filled('outputs')) {
                foreach ($request->outputs as $output) {
                    if (isset($output['id'])) {
                        // Update existing output
                        $outputModel = SemiFinishedUsageRequestOutput::find($output['id']);
                        if ($outputModel) {
                            $productId = $output['product_id'] ?? $output['finished_product_id'] ?? null;
                            $payload = [
                                // Persist using correct column name per schema
                                'product_id' => $productId,
                                'planned_quantity' => $output['planned_quantity'],
                                'notes' => $output['notes'] ?? null,
                            ];
                            if (array_key_exists('actual_quantity', $output)) {
                                $payload['actual_quantity'] = $output['actual_quantity'];
                            }
                            $outputModel->update($payload);
                            $updatedOutputIds[] = $outputModel->id;
                        }
                    } else {
                        $productId = $output['product_id'] ?? $output['finished_product_id'] ?? null;
                        if (!empty($productId) && !empty($output['planned_quantity'])) {
                            // Create new output
                            $o = SemiFinishedUsageRequestOutput::create([
                                'semi_finished_request_id' => $semiFinishedUsageRequest->id,
                                'product_id' => $productId,
                                'planned_quantity' => $output['planned_quantity'],
                                'actual_quantity' => $output['actual_quantity'] ?? null,
                                'notes' => $output['notes'] ?? null,
                            ]);
                            $updatedOutputIds[] = $o->id;
                        }
                    }
                }
            }

            // Delete removed outputs
            $outputsToDelete = array_diff($existingOutputIds, $updatedOutputIds);
            if (count($outputsToDelete) > 0) {
                SemiFinishedUsageRequestOutput::whereIn('id', $outputsToDelete)->delete();
            }

            DB::commit();

            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('success', 'Permintaan penggunaan bahan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Process approval of a material usage request
     */
    public function approve(Request $request, SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        if ($semiFinishedUsageRequest->status != SemiFinishedUsageRequest::STATUS_PENDING) {
            return $this->respond($request, false, 'Hanya permintaan dengan status menunggu persetujuan yang dapat disetujui.');
        }

        $user = Auth::user();
        $canApprove = $user->hasAnyRole(['admin', 'super-admin', 'Admin', 'Super Admin', 'Manager', 'Kepala Toko']);
        if (!$canApprove) {
            return $this->respond($request, false, 'Anda tidak memiliki izin untuk menyetujui permintaan ini.');
        }

        try {
            DB::beginTransaction();

            $semiFinishedUsageRequest->load(['items.semiFinishedProduct']);
            $branchId = $semiFinishedUsageRequest->requesting_branch_id;

            foreach ($semiFinishedUsageRequest->items as $item) {
                $product = $item->semiFinishedProduct;
                if (!$product) {
                    throw new \Exception('Produk setengah jadi tidak ditemukan untuk salah satu item.');
                }

                $available = $product->getCurrentStockForBranch($branchId);
                if ($available < $item->quantity) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi. Tersedia: {$available}, diminta: {$item->quantity}.");
                }
            }

            foreach ($semiFinishedUsageRequest->items as $item) {
                $product = $item->semiFinishedProduct;
                $product->updateStock($item->quantity, 'out', $branchId);

                if (Schema::hasTable('stock_movements')) {
                    \App\Models\StockMovement::create([
                        'semi_finished_product_id' => $product->id,
                        'branch_id' => $branchId,
                        'type' => 'out',
                        'movement_category' => 'usage',
                        'quantity' => $item->quantity,
                        'reference_id' => $semiFinishedUsageRequest->id,
                        'reference_type' => 'semi_finished_usage_request',
                        'notes' => "Persetujuan Permintaan Penggunaan Bahan #{$semiFinishedUsageRequest->request_number}",
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            $semiFinishedUsageRequest->update([
                'status' => SemiFinishedUsageRequest::STATUS_APPROVED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_notes' => $request->input('approval_note'),
            ]);

            DB::commit();

            return $this->respond($request, true, 'Permintaan disetujui dan stok telah dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respond($request, false, 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function respond(Request $request, bool $success, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
            ], $success ? 200 : 400);
        }

        return redirect()
            ->back()
            ->with($success ? 'success' : 'error', $message);
    }

    /**
     * Process rejection of a material usage request
     */
    public function reject(Request $request, SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $this->assertRetailBranchOnly();
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Only pending requests can be rejected
        if ($semiFinishedUsageRequest->status != SemiFinishedUsageRequest::STATUS_PENDING) {
            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('error', 'Hanya permintaan dengan status menunggu persetujuan yang dapat ditolak.');
        }

        // Only privileged roles can reject
        $user = Auth::user();
        $canReject = $user->hasRole('admin') || $user->hasRole('super-admin') ||
            $user->hasRole('Admin') || $user->hasRole('Super Admin') ||
            $user->hasRole('Manager') || $user->hasRole('Kepala Toko');
        if (!$canReject) {
            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('error', 'Anda tidak memiliki izin untuk menolak permintaan ini.');
        }

        try {
            DB::beginTransaction();

            $semiFinishedUsageRequest->update([
                'status' => SemiFinishedUsageRequest::STATUS_REJECTED,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            DB::commit();

            // If coming from approvals inbox, return there preserving filters
            if ($request->filled('return_to') && $request->input('return_to') === 'approvals') {
                return redirect()
                    ->route('semi-finished-usage-approvals.index', [
                        'status' => $request->input('status', SemiFinishedUsageRequest::STATUS_PENDING),
                        'branch_id' => $request->input('branch_id', 'all'),
                    ])
                    ->with('success', 'Permintaan penggunaan bahan telah ditolak.');
            }

            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('success', 'Permintaan penggunaan bahan telah ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Process material usage request to mark it as processing
     */
    public function process(Request $request, SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $this->assertRetailBranchOnly();
        // Only approved requests can be marked as processing
        if ($semiFinishedUsageRequest->status != SemiFinishedUsageRequest::STATUS_APPROVED) {
            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('error', 'Hanya permintaan yang sudah disetujui yang dapat diproses.');
        }

        try {
            DB::beginTransaction();

            $semiFinishedUsageRequest->update([
                'status' => SemiFinishedUsageRequest::STATUS_PROCESSING,
            ]);

            DB::commit();

            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('success', 'Permintaan penggunaan bahan sedang diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Complete a material usage request and subtract stock
     */
    public function complete(Request $request, SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $this->assertRetailBranchOnly();
        // Only processing requests can be completed
        if ($semiFinishedUsageRequest->status != SemiFinishedUsageRequest::STATUS_PROCESSING) {
            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('error', 'Hanya permintaan yang sedang diproses yang dapat diselesaikan.');
        }

        try {
            DB::beginTransaction();

            // Stock has been deducted at approval stage; here we only finalize the status
            $semiFinishedUsageRequest->update([
                'status' => SemiFinishedUsageRequest::STATUS_COMPLETED,
            ]);

            DB::commit();

            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('success', 'Permintaan penggunaan bahan telah selesai diproses.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Cancel a material usage request
     */
    public function cancel(Request $request, SemiFinishedUsageRequest $semiFinishedUsageRequest)
    {
        $this->assertRetailBranchOnly();
        // Only pending or approved requests can be cancelled
        if (!in_array($semiFinishedUsageRequest->status, [
            SemiFinishedUsageRequest::STATUS_PENDING,
            SemiFinishedUsageRequest::STATUS_APPROVED
        ])) {
            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('error', 'Hanya permintaan yang belum diproses yang dapat dibatalkan.');
        }

        try {
            DB::beginTransaction();

            $semiFinishedUsageRequest->update([
                'status' => SemiFinishedUsageRequest::STATUS_CANCELLED,
            ]);

            DB::commit();

            return redirect()
                ->route('semi-finished-usage-requests.show', $semiFinishedUsageRequest)
                ->with('success', 'Permintaan penggunaan bahan telah dibatalkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
