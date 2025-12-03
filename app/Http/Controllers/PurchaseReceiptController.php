<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReceipt;
use App\Models\PurchaseReceiptItem;
use App\Models\PurchaseReceiptAdditionalCost;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PurchaseReceiptController extends Controller
{
    /**
     * Build base filtered query for purchase receipts based on request parameters.
     * Reused by index() and exportCsv() to avoid duplication.
     */
    protected function buildFilteredQuery(Request $request)
    {
        $query = PurchaseReceipt::query();

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }
        if ($request->filled('start_date')) {
            $query->whereDate('receipt_date', '>=', $request->get('start_date'));
        }
        if ($request->filled('end_date')) {
            $query->whereDate('receipt_date', '<=', $request->get('end_date'));
        }
        if ($request->filled('q')) {
            $term = trim($request->get('q'));
            $query->where(function ($q) use ($term) {
                $q->where('receipt_number', 'like', "%{$term}%")
                    ->orWhereHas('purchaseOrder', function ($qp) use ($term) {
                        $qp->where('order_number', 'like', "%{$term}%")
                            ->orWhere('order_code', 'like', "%{$term}%");
                    })
                    ->orWhereHas('purchaseOrder.supplier', function ($qs) use ($term) {
                        $qs->where('name', 'like', "%{$term}%");
                    });
            });
        }

        return $query;
    }

    /**
     * Display a listing of the purchase receipts
     */
    public function index(Request $request)
    {
        $query = PurchaseReceipt::with(['purchaseOrder.supplier', 'receiver']);

        $columns = [
            ['key' => 'receipt_number', 'label' => 'No Penerimaan'],
            ['key' => 'receipt_date', 'label' => 'Tanggal'],
            ['key' => 'purchase_order_id', 'label' => 'Pesanan'],
            ['key' => 'supplier', 'label' => 'Supplier'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'received_by', 'label' => 'Penerima'],
            ['key' => 'total_amount', 'label' => 'Total Bayar'],
        ];

        if ($request->filled('search')) {
            $term = trim($request->get('search'));
            $query->where(function ($q) use ($term) {
                $q->where('receipt_number', 'like', "%{$term}%")
                    ->orWhereHas('purchaseOrder', function ($qp) use ($term) {
                        $qp->where('order_number', 'like', "%{$term}%")
                            ->orWhere('order_code', 'like', "%{$term}%");
                    })
                    ->orWhereHas('purchaseOrder.supplier', function ($qs) use ($term) {
                        $qs->where('name', 'like', "%{$term}%");
                    });
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
                case 'supplier':
                    $query->leftJoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_receipts.purchase_order_id')
                        ->leftJoin('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')
                        ->orderBy('suppliers.name', $sortDir)
                        ->select('purchase_receipts.*');
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        }

        // filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('receipt_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('receipt_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('receipt_date', '<=', $request->end_date);
        }

        // Sorting
        $sort = $request->get('sort', 'receipt_date');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['receipt_number', 'receipt_date', 'status', 'created_at', 'total_payment'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'receipt_date';
        }

        // Aggregate totals for filtered receipts (prefer snapshot columns)
        $filteredIds = (clone $query)->pluck('id');
        $filteredReceiptsCount = $filteredIds->count();
        $totalItemsAmount = 0.0;
        $totalAdditionalCosts = 0.0;
        $totalDiscount = 0.0;
        $totalTax = 0.0;
        $totalBelanja = 0.0;
        if ($filteredReceiptsCount > 0) {
            // Use stored snapshots for performance
            $totalItemsAmount = (float) PurchaseReceipt::whereIn('id', $filteredIds)
                ->selectRaw('COALESCE(SUM(subtotal_items), 0) AS s')
                ->value('s');
            $totalAdditionalCosts = (float) PurchaseReceipt::whereIn('id', $filteredIds)
                ->selectRaw('COALESCE(SUM(additional_cost_total), 0) AS s')
                ->value('s');
            $totalDiscount = (float) PurchaseReceipt::whereIn('id', $filteredIds)
                ->selectRaw('COALESCE(SUM(discount_amount), 0) AS s')
                ->value('s');
            $totalTax = (float) PurchaseReceipt::whereIn('id', $filteredIds)
                ->selectRaw('COALESCE(SUM(tax_amount), 0) AS s')
                ->value('s');
            // Total belanja prefers total_amount; fallback to formula from snapshots
            $totalBelanja = (float) PurchaseReceipt::whereIn('id', $filteredIds)
                ->selectRaw('COALESCE(SUM(COALESCE(total_amount, subtotal_items + additional_cost_total - discount_amount + tax_amount)), 0) AS s')
                ->value('s');
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $purchaseReceipts */
        $purchaseReceipts = $query->paginate(10);

        // Pending (ordered) purchase orders without any receipt yet
        $pendingOrders = PurchaseOrder::where('status', PurchaseOrder::STATUS_ORDERED)
            ->whereDoesntHave('receipt')
            ->with('supplier')
            ->orderBy('order_date', 'desc')
            ->get();

        $statuses = [
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            'partial' => 'Sebagian',
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
                'data' => $purchaseReceipts->items(),
                'links' => (string) $purchaseReceipts->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('purchase-receipts.index', compact(
            'purchaseReceipts',
            'pendingOrders',
            'totalItemsAmount',
            'totalAdditionalCosts',
            'totalDiscount',
            'totalTax',
            'totalBelanja',
            'filteredReceiptsCount',
            'columns',
            'selects'
        ));
    }

    /**
     * Export filtered purchase receipts to CSV (Excel-friendly) using snapshot totals.
     */
    public function exportCsv(Request $request)
    {
        $sort = $request->get('sort', 'receipt_date');
        $direction = strtolower($request->get('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['receipt_number', 'receipt_date', 'status', 'created_at', 'total_payment'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'receipt_date';
        }

        $query = $this->buildFilteredQuery($request)
            ->with(['purchaseOrder.supplier', 'receiver']);

        if ($sort === 'total_payment') {
            $query->select('purchase_receipts.*')
                ->selectRaw('COALESCE(
                    total_amount,
                    (
                        (SELECT COALESCE(SUM(pri.received_quantity * pri.unit_price), 0)
                         FROM purchase_receipt_items pri
                         WHERE pri.purchase_receipt_id = purchase_receipts.id)
                        +
                        (SELECT COALESCE(SUM(prac.amount), 0)
                         FROM purchase_receipt_additional_costs prac
                         WHERE prac.purchase_receipt_id = purchase_receipts.id)
                    )
                ) AS total_payment');
        }

        $receipts = $query->orderBy($sort, $direction)->get();

        $filename = 'purchase_receipts_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($receipts) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($out, "\xEF\xBB\xBF");
            // Header row
            fputcsv($out, [
                'No. Penerimaan',
                'Tanggal',
                'Supplier',
                'Status',
                'Penerima',
                'Subtotal Barang',
                'Biaya Tambahan',
                'Diskon',
                'Pajak',
                'Total Bayar'
            ]);

            foreach ($receipts as $r) {
                $total = method_exists($r, 'computeTotalPayment') ? $r->computeTotalPayment() : (float) ($r->total_amount ?? 0);
                fputcsv($out, [
                    $r->receipt_number,
                    optional($r->receipt_date)->format('Y-m-d'),
                    optional(optional($r->purchaseOrder)->supplier)->name,
                    $r->status,
                    optional($r->receiver)->name,
                    (float) ($r->subtotal_items ?? 0),
                    (float) ($r->additional_cost_total ?? 0),
                    (float) ($r->discount_amount ?? 0),
                    (float) ($r->tax_amount ?? 0),
                    (float) $total,
                ]);
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Show the form for creating a new purchase receipt
     */
    public function create(Request $request)
    {
        // Check if we have a purchase order ID
        $purchaseOrderId = $request->query('purchase_order_id');

        // Show a list of ordered purchase orders that don't have a completed receipt
        $pendingOrders = PurchaseOrder::where('status', PurchaseOrder::STATUS_ORDERED)
            ->whereDoesntHave('receipt', function ($query) {
                $query->whereIn('status', [
                    PurchaseReceipt::STATUS_ACCEPTED,
                    PurchaseReceipt::STATUS_REJECTED
                ]);
            })
            ->with('supplier')
            ->get();

        // The create view will handle selecting PO and fetching its items via API
        return view('purchase-receipts.create', compact('pendingOrders'));
    }

    /**
     * Store a newly created purchase receipt in storage
     */
    public function store(Request $request)
    {
        $rules = [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'receipt_date' => 'required|date',
            // status field may be posted by UI but will be derived on backend
            'status' => ['required', Rule::in(['accepted', 'rejected', 'partial'])],
            'notes' => 'nullable|string',
            // Photo is required on create
            'receipt_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'items' => 'required|array',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.received_quantity' => 'required|numeric|min:0',
            // rejected_quantity and item_status will be calculated server-side
            'items.*.rejected_quantity' => 'nullable|numeric|min:0',
            'items.*.item_status' => ['nullable', Rule::in(['accepted', 'rejected', 'partial'])],
            'items.*.condition_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'items.*.notes' => 'nullable|string',
            // Additional costs
            'additional_costs' => 'nullable|array',
            'additional_costs.*.cost_name' => 'required_with:additional_costs|string|max:100',
            'additional_costs.*.amount' => 'required_with:additional_costs|numeric|min:0',
            'additional_costs.*.notes' => 'nullable|string|max:255',
            // Optional discount and tax
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ];

        $messages = [
            'purchase_order_id.required' => 'Pesanan pembelian wajib dipilih.',
            'purchase_order_id.exists' => 'Pesanan pembelian tidak ditemukan.',
            'receipt_date.required' => 'Tanggal penerimaan wajib diisi.',
            'receipt_date.date' => 'Tanggal penerimaan tidak valid.',
            'status.required' => 'Status penerimaan wajib diisi.',
            'status.in' => 'Status penerimaan tidak valid.',
            'receipt_photo.required' => 'Foto bukti penerimaan wajib diunggah.',
            'receipt_photo.image' => 'Foto bukti penerimaan harus berupa gambar.',
            'receipt_photo.mimes' => 'Foto bukti penerimaan harus berformat JPG atau PNG.',
            'receipt_photo.max' => 'Ukuran foto bukti penerimaan maksimal 2MB.',
            'items.required' => 'Detail item penerimaan wajib diisi.',
            'items.array' => 'Format detail item tidak valid.',
            'items.*.purchase_order_item_id.required' => 'Item pesanan tidak valid.',
            'items.*.purchase_order_item_id.exists' => 'Item pesanan tidak ditemukan.',
            'items.*.received_quantity.required' => 'Jumlah diterima pada setiap item wajib diisi.',
            'items.*.received_quantity.numeric' => 'Jumlah diterima harus berupa angka.',
            'items.*.received_quantity.min' => 'Jumlah diterima minimal 0.',
            'items.*.rejected_quantity.numeric' => 'Jumlah ditolak harus berupa angka.',
            'items.*.rejected_quantity.min' => 'Jumlah ditolak minimal 0.',
            'items.*.item_status.in' => 'Status item tidak valid.',
            'items.*.condition_photo.required' => 'Foto kondisi item wajib diunggah untuk setiap item.',
            'items.*.condition_photo.image' => 'Foto kondisi item harus berupa gambar.',
            'items.*.condition_photo.mimes' => 'Foto kondisi item harus berformat JPG atau PNG.',
            'items.*.condition_photo.max' => 'Ukuran foto kondisi item maksimal 2MB.',
            'additional_costs.array' => 'Format biaya tambahan tidak valid.',
            'additional_costs.*.cost_name.required_with' => 'Nama biaya wajib diisi ketika menambah baris biaya.',
            'additional_costs.*.amount.required_with' => 'Nominal biaya wajib diisi ketika menambah baris biaya.',
            'additional_costs.*.amount.numeric' => 'Nominal biaya harus berupa angka.',
            'additional_costs.*.amount.min' => 'Nominal biaya minimal 0.',
            'discount_amount.numeric' => 'Diskon harus berupa angka.',
            'discount_amount.min' => 'Diskon minimal 0.',
            'tax_amount.numeric' => 'Pajak harus berupa angka.',
            'tax_amount.min' => 'Pajak minimal 0.',
        ];

        $attributes = [
            'receipt_photo' => 'foto bukti penerimaan',
            'receipt_date' => 'tanggal penerimaan',
            'items.*.received_quantity' => 'jumlah diterima',
        ];

        $request->validate($rules, $messages, $attributes);

        try {
            DB::beginTransaction();

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('receipt_photo')) {
                $photoPath = $request->file('receipt_photo')->store('receipts', 'public');
            }

            // Create the purchase receipt (status will be updated after items are saved)
            $receipt = PurchaseReceipt::create([
                'purchase_order_id' => $request->purchase_order_id,
                'received_by' => Auth::id(),
                'receipt_date' => $request->receipt_date,
                'status' => PurchaseReceipt::STATUS_PARTIAL, // temporary; will be derived
                'notes' => $request->notes,
                'receipt_photo' => $photoPath
            ]);

            // Create receipt items with backend normalization
            foreach ($request->items as $idx => $itemData) {
                $purchaseOrderItem = PurchaseOrderItem::findOrFail($itemData['purchase_order_item_id']);
                $ordered = (float) $purchaseOrderItem->quantity;
                $received = (float) ($itemData['received_quantity'] ?? 0);
                // Enforce received within [0, ordered]
                if ($received < 0) {
                    $received = 0.0;
                }
                if ($received > $ordered) {
                    $received = $ordered;
                }
                $rejected = round($ordered - $received, 2);
                $computedStatus = $received == 0.0
                    ? PurchaseReceiptItem::STATUS_REJECTED
                    : ($received == $ordered ? PurchaseReceiptItem::STATUS_ACCEPTED : PurchaseReceiptItem::STATUS_PARTIAL);

                // Handle item photo upload (nested file input)
                $itemPhotoPath = null;
                $uploadedFile = $request->file("items.$idx.condition_photo");
                if ($uploadedFile) {
                    $itemPhotoPath = $uploadedFile->store('receipt-items', 'public');
                }

                PurchaseReceiptItem::create([
                    'purchase_receipt_id' => $receipt->id,
                    'purchase_order_item_id' => $itemData['purchase_order_item_id'],
                    'raw_material_id' => $purchaseOrderItem->raw_material_id,
                    'ordered_quantity' => $ordered,
                    'received_quantity' => $received,
                    'rejected_quantity' => $rejected,
                    'unit_price' => $purchaseOrderItem->unit_price,
                    'item_status' => $computedStatus,
                    'condition_photo' => $itemPhotoPath,
                    'notes' => $itemData['notes'] ?? null
                ]);
            }

            // Create additional costs if provided
            if ($request->filled('additional_costs') && is_array($request->additional_costs)) {
                foreach ($request->additional_costs as $cost) {
                    // Skip empty rows (in case of accidental empty inputs)
                    $name = $cost['cost_name'] ?? null;
                    $amount = $cost['amount'] ?? null;
                    if ($name === null && $amount === null) {
                        continue;
                    }
                    PurchaseReceiptAdditionalCost::create([
                        'purchase_receipt_id' => $receipt->id,
                        'cost_name' => $name,
                        'amount' => $amount,
                        'notes' => $cost['notes'] ?? null,
                    ]);
                }
            }

            // Compute and snapshot totals (server-side authoritative)
            $discount = (float) ($request->input('discount_amount', 0));
            $tax = (float) ($request->input('tax_amount', 0));
            $receipt->recalcAndSnapshotTotals($discount, $tax);

            // Derive and persist overall receipt status from items
            $receipt->load('items');
            $receipt->status = $receipt->determineStatusFromItems();
            $receipt->save();

            // Update stock based on received items (accepted/partial only)
            $receipt->updateStock();

            // Sync purchase order status based on receipt status
            $purchaseOrder = $receipt->purchaseOrder;
            $purchaseOrder->syncStatusFromReceipt();

            DB::commit();

            // Send WhatsApp group notification (privacy-safe); errors are ignored
            try {
                $receipt->sendWhatsAppNotificationToGroup();
            } catch (\Exception $e) {
            }

            return redirect()->route('purchase-receipts.index')
                ->with('success', 'Purchase receipt created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create purchase receipt: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified purchase receipt
     */
    public function show(PurchaseReceipt $purchaseReceipt)
    {
        $purchaseReceipt->load([
            'purchaseOrder.supplier',
            'items.rawMaterial.unit',
            'items.purchaseOrderItem',
            'receiver',
            'additionalCosts'
        ]);

        return view('purchase-receipts.show', compact('purchaseReceipt'));
    }

    /**
     * Show the form for editing the specified purchase receipt
     */
    public function edit(PurchaseReceipt $purchaseReceipt)
    {
        // Allow editing for all statuses

        $purchaseReceipt->load([
            'purchaseOrder.items.rawMaterial.unit',
            'purchaseOrder.supplier',
            'items',
            'additionalCosts'
        ]);

        return view('purchase-receipts.edit', compact('purchaseReceipt'));
    }

    /**
     * Update the specified purchase receipt in storage
     */
    public function update(Request $request, PurchaseReceipt $purchaseReceipt)
    {
        $rules = [
            'receipt_date' => 'required|date',
            // status will be derived from items
            'status' => ['required', Rule::in(['accepted', 'rejected', 'partial'])],
            'notes' => 'nullable|string',
            // Photo is required if there is no existing one
            'receipt_photo' => ($purchaseReceipt->receipt_photo ? 'nullable' : 'required') . '|image|mimes:jpeg,png,jpg|max:2048',
            'items' => 'required|array',
            'items.*.received_quantity' => 'required|numeric|min:0',
            // backend will compute these
            'items.*.rejected_quantity' => 'nullable|numeric|min:0',
            'items.*.item_status' => ['nullable', Rule::in(['accepted', 'rejected', 'partial'])],
            'items.*.condition_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'items.*.notes' => 'nullable|string',
            // Additional costs
            'additional_costs' => 'nullable|array',
            'additional_costs.*.cost_name' => 'required_with:additional_costs|string|max:100',
            'additional_costs.*.amount' => 'required_with:additional_costs|numeric|min:0',
            'additional_costs.*.notes' => 'nullable|string|max:255',
            // Optional discount and tax
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
        ];

        // Conditionally require condition_photo per item if the item has no existing photo
        try {
            $purchaseReceipt->loadMissing('items');
            foreach ((array) $request->input('items', []) as $itemId => $itemData) {
                $modelItem = $purchaseReceipt->items->firstWhere('id', (int) $itemId);
                if ($modelItem && empty($modelItem->condition_photo)) {
                    $rules["items.$itemId.condition_photo"] = 'required|image|mimes:jpeg,png,jpg|max:2048';
                }
            }
        } catch (\Throwable $e) {
            // Fallback: keep nullable rule if anything goes wrong
        }

        $messages = [
            'receipt_date.required' => 'Tanggal penerimaan wajib diisi.',
            'receipt_date.date' => 'Tanggal penerimaan tidak valid.',
            'status.required' => 'Status penerimaan wajib diisi.',
            'status.in' => 'Status penerimaan tidak valid.',
            'receipt_photo.required' => 'Foto bukti penerimaan wajib diunggah.',
            'receipt_photo.image' => 'Foto bukti penerimaan harus berupa gambar.',
            'receipt_photo.mimes' => 'Foto bukti penerimaan harus berformat JPG atau PNG.',
            'receipt_photo.max' => 'Ukuran foto bukti penerimaan maksimal 2MB.',
            'items.required' => 'Detail item penerimaan wajib diisi.',
            'items.array' => 'Format detail item tidak valid.',
            'items.*.received_quantity.required' => 'Jumlah diterima pada setiap item wajib diisi.',
            'items.*.received_quantity.numeric' => 'Jumlah diterima harus berupa angka.',
            'items.*.received_quantity.min' => 'Jumlah diterima minimal 0.',
            'items.*.rejected_quantity.numeric' => 'Jumlah ditolak harus berupa angka.',
            'items.*.rejected_quantity.min' => 'Jumlah ditolak minimal 0.',
            'items.*.item_status.in' => 'Status item tidak valid.',
            'items.*.condition_photo.required' => 'Foto kondisi item wajib diunggah untuk item yang belum memiliki foto.',
            'items.*.condition_photo.image' => 'Foto kondisi item harus berupa gambar.',
            'items.*.condition_photo.mimes' => 'Foto kondisi item harus berformat JPG atau PNG.',
            'items.*.condition_photo.max' => 'Ukuran foto kondisi item maksimal 2MB.',
            'additional_costs.array' => 'Format biaya tambahan tidak valid.',
            'additional_costs.*.cost_name.required_with' => 'Nama biaya wajib diisi ketika menambah baris biaya.',
            'additional_costs.*.amount.required_with' => 'Nominal biaya wajib diisi ketika menambah baris biaya.',
            'additional_costs.*.amount.numeric' => 'Nominal biaya harus berupa angka.',
            'additional_costs.*.amount.min' => 'Nominal biaya minimal 0.',
            'discount_amount.numeric' => 'Diskon harus berupa angka.',
            'discount_amount.min' => 'Diskon minimal 0.',
            'tax_amount.numeric' => 'Pajak harus berupa angka.',
            'tax_amount.min' => 'Pajak minimal 0.',
        ];

        $attributes = [
            'receipt_photo' => 'foto bukti penerimaan',
            'receipt_date' => 'tanggal penerimaan',
            'items.*.received_quantity' => 'jumlah diterima',
        ];

        $request->validate($rules, $messages, $attributes);

        try {
            DB::beginTransaction();

            // Handle photo upload
            if ($request->hasFile('receipt_photo')) {
                // Delete old photo
                if ($purchaseReceipt->receipt_photo) {
                    Storage::disk('public')->delete($purchaseReceipt->receipt_photo);
                }
                $purchaseReceipt->receipt_photo = $request->file('receipt_photo')->store('receipts', 'public');
            }

            // Snapshot old accepted quantities for delta stock adjustment
            $purchaseReceipt->loadMissing('items');
            $oldSnapshot = $purchaseReceipt->getAcceptedQuantitiesSnapshot();

            // Update receipt meta (status will be recalculated later)
            $purchaseReceipt->update([
                'receipt_date' => $request->receipt_date,
                'notes' => $request->notes,
                'receipt_photo' => $purchaseReceipt->receipt_photo
            ]);

            // Update items
            foreach ($request->items as $itemId => $itemData) {
                $item = $purchaseReceipt->items()->findOrFail($itemId);
                $ordered = (float) $item->ordered_quantity; // use stored ordered qty
                $received = (float) ($itemData['received_quantity'] ?? 0);
                if ($received < 0) {
                    $received = 0.0;
                }
                if ($received > $ordered) {
                    $received = $ordered;
                }
                $rejected = round($ordered - $received, 2);
                $computedStatus = $received == 0.0
                    ? PurchaseReceiptItem::STATUS_REJECTED
                    : ($received == $ordered ? PurchaseReceiptItem::STATUS_ACCEPTED : PurchaseReceiptItem::STATUS_PARTIAL);

                // Handle item photo upload (nested file input)
                $uploadedFile = $request->file("items.$itemId.condition_photo");
                if ($uploadedFile) {
                    // Delete old photo
                    if ($item->condition_photo) {
                        Storage::disk('public')->delete($item->condition_photo);
                    }
                    $item->condition_photo = $uploadedFile->store('receipt-items', 'public');
                }

                $item->update([
                    'received_quantity' => $received,
                    'rejected_quantity' => $rejected,
                    'item_status' => $computedStatus,
                    'condition_photo' => $item->condition_photo,
                    'notes' => $itemData['notes'] ?? null
                ]);
            }

            // Derive and persist overall receipt status from items
            $purchaseReceipt->load('items');
            $purchaseReceipt->status = $purchaseReceipt->determineStatusFromItems();
            $purchaseReceipt->save();

            // Adjust stock based on delta of accepted quantities
            $newSnapshot = $purchaseReceipt->getAcceptedQuantitiesSnapshot();
            // Aggregate all raw_material_ids
            $allRawIds = array_unique(array_merge(array_keys($oldSnapshot), array_keys($newSnapshot)));
            foreach ($allRawIds as $rmId) {
                $oldQty = (float) ($oldSnapshot[$rmId] ?? 0);
                $newQty = (float) ($newSnapshot[$rmId] ?? 0);
                $delta = $newQty - $oldQty;
                if ($delta === 0.0) {
                    continue;
                }
                $raw = \App\Models\RawMaterial::find($rmId);
                if ($raw) {
                    if ($delta > 0) {
                        $raw->updateStock($delta, 'in');
                    } else {
                        $raw->updateStock(abs($delta), 'out');
                    }
                }
            }

            // Sync purchase order status after updates
            $purchaseReceipt->purchaseOrder->syncStatusFromReceipt();

            // Replace additional costs with new data if provided
            $purchaseReceipt->additionalCosts()->delete();
            if ($request->filled('additional_costs') && is_array($request->additional_costs)) {
                foreach ($request->additional_costs as $cost) {
                    $name = $cost['cost_name'] ?? null;
                    $amount = $cost['amount'] ?? null;
                    if ($name === null && $amount === null) {
                        continue;
                    }
                    PurchaseReceiptAdditionalCost::create([
                        'purchase_receipt_id' => $purchaseReceipt->id,
                        'cost_name' => $name,
                        'amount' => $amount,
                        'notes' => $cost['notes'] ?? null,
                    ]);
                }
            }

            // Recompute and snapshot totals after items/additional costs changes
            $discount = (float) ($request->input('discount_amount', 0));
            $tax = (float) ($request->input('tax_amount', 0));
            $purchaseReceipt->recalcAndSnapshotTotals($discount, $tax);

            DB::commit();

            // Send WhatsApp group notification (privacy-safe); errors are ignored
            try {
                $purchaseReceipt->sendWhatsAppNotificationToGroup();
            } catch (\Exception $e) {
            }

            return redirect()->route('purchase-receipts.show', $purchaseReceipt)
                ->with('success', 'Purchase receipt updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update purchase receipt: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified purchase receipt from storage
     */
    public function destroy(PurchaseReceipt $purchaseReceipt)
    {
        try {
            DB::beginTransaction();

            // Delete photos
            if ($purchaseReceipt->receipt_photo) {
                Storage::disk('public')->delete($purchaseReceipt->receipt_photo);
            }

            foreach ($purchaseReceipt->items as $item) {
                if ($item->condition_photo) {
                    Storage::disk('public')->delete($item->condition_photo);
                }
            }

            // Capture related purchase order before deletion
            $purchaseOrder = $purchaseReceipt->purchaseOrder;

            // Delete the receipt
            $purchaseReceipt->delete();

            // Sync purchase order status after receipt deletion
            if ($purchaseOrder) {
                $purchaseOrder->syncStatusFromReceipt();
            }

            DB::commit();

            return redirect()->route('purchase-receipts.index')
                ->with('success', 'Purchase receipt deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete purchase receipt: ' . $e->getMessage()]);
        }
    }

    /**
     * API: Get purchase order items by purchase order ID
     */
    public function itemsByPurchaseOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:purchase_orders,id',
        ]);

        $purchaseOrder = PurchaseOrder::with(['items.rawMaterial.unit'])->findOrFail($request->order_id);

        $items = collect($purchaseOrder->items)->map(function ($item) {
            return [
                'id' => $item->id,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'unit_name' => $item->unit_name,
                'raw_material' => [
                    'id' => $item->rawMaterial->id,
                    'name' => $item->rawMaterial->name,
                    'unit' => [
                        'id' => optional($item->rawMaterial->unit)->id,
                        'name' => optional($item->rawMaterial->unit)->name,
                    ],
                ],
            ];
        })->values();

        return response()->json([
            'items' => $items,
        ]);
    }
}
