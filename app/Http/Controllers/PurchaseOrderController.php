<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        // Ensure all purchase order actions require authentication
        $this->middleware('auth');
    }

    /**
     * Display a listing of purchase orders with optional filters
     */
    public function index(Request $request)
    {
        // Base query with relationships
        $query = PurchaseOrder::with(['supplier', 'items.rawMaterial', 'creator']);

        $columns = [
            ['key' => 'order_number', 'label' => 'Nomor Order'],
            ['key' => 'supplier', 'label' => 'Supplier'],
            ['key' => 'order_date', 'label' => 'Tanggal'],
            ['key' => 'total_amount', 'label' => 'Total'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'created_by', 'label' => 'Dibuat Oleh'],
        ];

        // === SEARCH ===
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhereHas('category', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('unit', fn($q2) => $q2->where('unit_name', 'like', "%{$search}%"))
                    ->orWhereHas('supplier', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
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
                    $query->leftjoin('suppliers', 'suppliers.id', '=', 'purchase_orders.supplier_id')
                        ->orderBy('suppliers.name', $sortDir)
                        ->select('purchase_orders.*');
                    break;

                default:
                    $query->orderBy($sortBy, $sortDir);
            }
        }

        // filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('order_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('order_date', '<=', $request->end_date);
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $purchaseOrders */
        $purchaseOrders = $query->paginate(10);

        // Suppliers for filter dropdown
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('name')->pluck('name', 'id')->toArray();

        $statuses = [
            'draft' => 'Draft',
            'ordered' => 'Ordered',
            'received' => 'Received',
            'partially_received' => 'Partially Received',
            'rejected' => 'Rejected',
        ];

        $selects = [
            [
                'name' => 'status',
                'label' => 'Semua Status',
                'options' => $statuses,
            ],
            [
                'name' => 'supplier_id',
                'label' => 'Semua Supplier',
                'options' => $suppliers,
            ],
        ];

        // === RESPONSE ===
        if ($request->ajax()) {
            return response()->json([
                'data' => $purchaseOrders->items(),
                'links' => (string) $purchaseOrders->links('vendor.pagination.tailwind'),
            ]);
        }

        return view('purchase-orders.index', [
            'purchaseOrders' => $purchaseOrders->items(),
            'selects' => $selects,
            'columns' => $columns,
            'pagination' => $purchaseOrders,
        ]);
    }

    /**
     * Show the form for creating a new purchase order
     */
    public function create()
    {
        // Only show active suppliers as per requirements
        $suppliers = Supplier::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get all raw materials with unit info for initial load
        $rawMaterials = RawMaterial::with(['unit', 'supplier'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('purchase-orders.create', compact('suppliers', 'rawMaterials'));
    }

    // The materialsBySupplier method is implemented further down in this class

    /**
     * Store a newly created purchase order
     */
    public function store(Request $request)
    {
        // Validate request with Indonesian messages
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date|before_or_equal:today',
            'requested_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',
            'submit_action' => 'required|in:save_draft,order_now',
        ], [
            'supplier_id.required' => 'Supplier harus dipilih',
            'supplier_id.exists' => 'Supplier tidak valid',
            'order_date.required' => 'Tanggal pesanan harus diisi',
            'order_date.date' => 'Format tanggal tidak valid',
            'order_date.before_or_equal' => 'Tanggal pesanan tidak boleh melebihi hari ini',
            'requested_delivery_date.date' => 'Format tanggal pengiriman tidak valid',
            'requested_delivery_date.after_or_equal' => 'Tanggal pengiriman harus sama atau setelah tanggal pesanan',
            'items.required' => 'Minimal harus ada satu item',
            'items.min' => 'Minimal harus ada satu item',
            'items.*.raw_material_id.required' => 'Bahan mentah harus dipilih',
            'items.*.raw_material_id.exists' => 'Bahan mentah tidak valid',
            'items.*.quantity.required' => 'Kuantitas harus diisi',
            'items.*.quantity.numeric' => 'Kuantitas harus berupa angka',
            'items.*.quantity.min' => 'Kuantitas minimal 0.01',
            'items.*.unit_price.required' => 'Harga satuan harus diisi',
            'items.*.unit_price.numeric' => 'Harga satuan harus berupa angka',
            'items.*.unit_price.min' => 'Harga satuan tidak boleh negatif',
            'submit_action.required' => 'Tipe aksi diperlukan',
            'submit_action.in' => 'Tipe aksi tidak valid',
        ]);

        try {
            DB::beginTransaction();

            // Determine if this is a draft or order based on the submit_action
            $status = 'draft';
            $isOrder = ($request->submit_action === 'order_now');

            if ($isOrder) {
                $status = 'ordered';
                $orderedAt = now();
            } else {
                $orderedAt = null;
            }

            // Generate a unique order code and number for both draft and orders
            $latestOrder = PurchaseOrder::latest('id')->first();
            $orderNumber = 'PO-' . date('Ymd') . '-' . sprintf('%04d', $latestOrder ? ($latestOrder->id + 1) : 1);
            $orderCode = 'PO' . date('ymd') . sprintf('%04d', $latestOrder ? ($latestOrder->id + 1) : 1);

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'requested_delivery_date' => $request->requested_delivery_date,
                'notes' => $request->notes,
                'status' => $status,
                'created_by' => Auth::id(),
                'order_number' => $orderNumber,
                'order_code' => $orderCode,
                'ordered_at' => $orderedAt,
            ]);

            // Calculate total and create items
            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                $totalPrice = floatval($itemData['quantity']) * floatval($itemData['unit_price']);
                $totalAmount += $totalPrice;

                // Get raw material for unit info
                $rawMaterial = RawMaterial::find($itemData['raw_material_id']);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'raw_material_id' => $itemData['raw_material_id'],
                    'quantity' => floatval($itemData['quantity']),
                    'unit_id' => $rawMaterial->unit_id,
                    'unit_name' => optional($rawMaterial->unit)->unit_name ?? 'Unit',
                    'unit_price' => floatval($itemData['unit_price']),
                    'total_price' => $totalPrice,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            // Update total amount
            $purchaseOrder->update(['total_amount' => $totalAmount]);

            // Handle WhatsApp message if this is an order
            if ($isOrder) {
                // Get supplier for WhatsApp
                $supplier = Supplier::find($request->supplier_id);

                if ($supplier && $supplier->phone) {
                    // Set flag that we attempted to send WhatsApp
                    $purchaseOrder->update(['whatsapp_sent' => true]);

                    // Prepare and send WhatsApp message
                    $this->sendOrderWhatsApp($purchaseOrder, $supplier);
                }
            }

            DB::commit();

            $successMessage = $isOrder ?
                'Purchase order berhasil dibuat dan dikirim ke supplier' :
                'Purchase order berhasil disimpan sebagai draft';

            return redirect()->route('purchase-orders.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase order
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.rawMaterial.unit', 'creator']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        // Only allow editing draft orders
        if ($purchaseOrder->status !== PurchaseOrder::STATUS_DRAFT) {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'Hanya pesanan dengan status draft yang dapat diedit');
        }

        $suppliers = Supplier::where('is_active', true)
            ->orderBy('name')
            ->get();

        $rawMaterials = RawMaterial::with(['unit', 'supplier'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $purchaseOrder->load(['items.rawMaterial']);

        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'rawMaterials'));
    }

    /**
     * Update the specified purchase order
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Only allow updating draft orders
        if ($purchaseOrder->status !== PurchaseOrder::STATUS_DRAFT) {
            return redirect()->route('purchase-orders.index')
                ->with('error', 'Hanya pesanan dengan status draft yang dapat diupdate');
        }

        // Use same validation as store
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date|before_or_equal:today',
            'requested_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',
            'submit_action' => 'required|in:save_draft,order_now',
        ], [
            'supplier_id.required' => 'Supplier harus dipilih',
            'supplier_id.exists' => 'Supplier tidak valid',
            'order_date.required' => 'Tanggal pesanan harus diisi',
            'order_date.date' => 'Format tanggal tidak valid',
            'order_date.before_or_equal' => 'Tanggal pesanan tidak boleh melebihi hari ini',
            'requested_delivery_date.date' => 'Format tanggal pengiriman tidak valid',
            'requested_delivery_date.after_or_equal' => 'Tanggal pengiriman harus sama atau setelah tanggal pesanan',
            'items.required' => 'Minimal harus ada satu item',
            'items.min' => 'Minimal harus ada satu item',
            'items.*.raw_material_id.required' => 'Bahan mentah harus dipilih',
            'items.*.raw_material_id.exists' => 'Bahan mentah tidak valid',
            'items.*.quantity.required' => 'Kuantitas harus diisi',
            'items.*.quantity.numeric' => 'Kuantitas harus berupa angka',
            'items.*.quantity.min' => 'Kuantitas minimal 0.01',
            'items.*.unit_price.required' => 'Harga satuan harus diisi',
            'items.*.unit_price.numeric' => 'Harga satuan harus berupa angka',
            'items.*.unit_price.min' => 'Harga satuan tidak boleh negatif',
            'submit_action.required' => 'Tipe aksi diperlukan',
            'submit_action.in' => 'Tipe aksi tidak valid',
        ]);

        try {
            DB::beginTransaction();

            // Determine action
            $isOrder = ($request->submit_action === 'order_now');
            $status = $isOrder ? PurchaseOrder::STATUS_ORDERED : PurchaseOrder::STATUS_DRAFT;
            $orderedAt = $isOrder ? now() : null;

            // Update purchase order
            $purchaseOrder->update([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'requested_delivery_date' => $request->requested_delivery_date,
                'notes' => $request->notes,
                'status' => $status,
                'ordered_at' => $orderedAt,
            ]);

            // Delete existing items
            $purchaseOrder->items()->delete();

            // Create new items
            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                $totalPrice = floatval($itemData['quantity']) * floatval($itemData['unit_price']);
                $totalAmount += $totalPrice;

                $rawMaterial = RawMaterial::find($itemData['raw_material_id']);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'raw_material_id' => $itemData['raw_material_id'],
                    'quantity' => floatval($itemData['quantity']),
                    'unit_id' => $rawMaterial->unit_id,
                    'unit_name' => optional($rawMaterial->unit)->unit_name ?? 'Unit',
                    'unit_price' => floatval($itemData['unit_price']),
                    'total_price' => $totalPrice,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            // Update total amount
            $purchaseOrder->update(['total_amount' => $totalAmount]);

            // If ordering now, send WhatsApp
            $whatsappSent = false;
            if ($isOrder) {
                $supplier = Supplier::find($request->supplier_id);
                if ($supplier && $supplier->phone) {
                    $whatsappSent = $this->sendOrderWhatsApp($purchaseOrder, $supplier);
                    if ($whatsappSent) {
                        $purchaseOrder->update(['whatsapp_sent' => true]);
                    }
                }
            }

            DB::commit();

            $message = $isOrder
                ? ('Purchase order berhasil diproses' . ($whatsappSent ? ' dan WhatsApp terkirim.' : '.'))
                : 'Purchase order berhasil diupdate';

            return redirect()->route('purchase-orders.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal mengupdate purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase order
     */
    public function destroy(Request $request, PurchaseOrder $purchaseOrder)
    {
        // Only allow deleting draft orders
        if ($purchaseOrder->status !== PurchaseOrder::STATUS_DRAFT) {
            $message = 'Hanya pesanan dengan status draft yang dapat dihapus';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return redirect()->route('purchase-orders.index')->with('error', $message);
        }

        try {
            $purchaseOrder->items()->delete();
            $purchaseOrder->delete();
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Purchase order berhasil dihapus']);
            }
            return redirect()->route('purchase-orders.index')->with('success', 'Purchase order berhasil dihapus');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus purchase order: ' . $e->getMessage()], 500);
            }
            return redirect()->route('purchase-orders.index')->with('error', 'Gagal menghapus purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Get raw materials by supplier (AJAX)
     */
    public function getMaterialsBySupplier(Request $request): JsonResponse
    {
        // Support both route param and query param for supplier_id
        $supplierId = $request->route('supplier_id') ?? $request->get('supplier_id');

        if (!$supplierId) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier ID diperlukan',
                'materials' => [],
                'data' => [],
                'count' => 0,
            ], 400);
        }

        try {
            $materials = RawMaterial::with(['unit', 'supplier'])
                ->where('supplier_id', $supplierId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(function ($material) {
                    return [
                        'id' => $material->id,
                        'name' => $material->name,
                        'code' => $material->code,
                        'unit' => [
                            'id' => optional($material->unit)->id,
                            'name' => optional($material->unit)->unit_name ?? '',
                            'unit_name' => optional($material->unit)->unit_name ?? '',
                            'abbreviation' => optional($material->unit)->abbreviation ?? null,
                        ],
                        'unit_price' => $material->unit_price ?? 0,
                        'supplier_id' => $material->supplier_id,
                    ];
                });

            return response()->json([
                'success' => true,
                'materials' => $materials,
                'data' => $materials,
                'count' => $materials->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data bahan mentah: ' . $e->getMessage(),
                'materials' => [],
                'data' => [],
            ], 500);
        }
    }

    /**
     * Get raw materials filtered by supplier (AJAX endpoint)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function materialsBySupplier(Request $request): JsonResponse
    {
        try {
            $supplierId = $request->get('supplier_id');

            $query = RawMaterial::with(['unit', 'supplier'])
                ->where('is_active', true);

            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }

            $materials = $query->orderBy('name')
                ->get()
                ->map(function ($material) {
                    return [
                        'id' => $material->id,
                        'name' => $material->name,
                        'code' => $material->code,
                        'unit' => [
                            'id' => optional($material->unit)->id,
                            'name' => optional($material->unit)->unit_name ?? '',
                            'unit_name' => optional($material->unit)->unit_name ?? '',
                            'abbreviation' => optional($material->unit)->abbreviation ?? null,
                        ],
                        'unit_price' => $material->unit_price ?? 0,
                        'supplier_id' => $material->supplier_id,
                        'supplier' => $material->supplier ? [
                            'id' => $material->supplier->id,
                            'name' => $material->supplier->name,
                        ] : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'materials' => $materials,
                'data' => $materials,
                'count' => $materials->count(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark purchase order as ordered
     */
    public function markAsOrdered(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== PurchaseOrder::STATUS_DRAFT || $purchaseOrder->items->count() === 0) {
            $message = 'Purchase order tidak dapat diorder.';
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return redirect()->route('purchase-orders.show', $purchaseOrder)->with('error', $message);
        }

        try {
            // Update status and ordered timestamp
            $purchaseOrder->status = PurchaseOrder::STATUS_ORDERED;
            $purchaseOrder->ordered_at = now();
            $purchaseOrder->save();

            // Send WhatsApp to supplier if phone available
            $whatsappSent = false;
            $supplier = $purchaseOrder->supplier;
            if ($supplier && $supplier->phone) {
                $whatsappSent = $this->sendOrderWhatsApp($purchaseOrder, $supplier);
                if ($whatsappSent) {
                    $purchaseOrder->whatsapp_sent = true;
                    $purchaseOrder->save();
                }
            }

            $message = 'Purchase order berhasil diproses' . ($whatsappSent ? ' dan WhatsApp terkirim.' : '.');

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'whatsapp_sent' => $whatsappSent,
                ]);
            }

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Status purchase order berhasil diubah menjadi ordered.');
        } catch (\Exception $e) {
            Log::error('markAsOrdered error: ' . $e->getMessage());
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengubah status purchase order: ' . $e->getMessage()], 500);
            }
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Gagal mengubah status purchase order: ' . $e->getMessage());
        }
    }

    /**
     * Print purchase order
     */
    public function print(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.rawMaterial.unit', 'creator']);

        return view('purchase-orders.print', compact('purchaseOrder'));
    }

    /**
     * Send purchase order to supplier via WhatsApp
     * 
     * @param PurchaseOrder $purchaseOrder
     * @param Supplier $supplier
     * @return bool
     */
    protected function sendOrderWhatsApp(PurchaseOrder $purchaseOrder, Supplier $supplier)
    {
        try {
            // Make sure we have a valid phone number
            if (empty($supplier->phone) || !preg_match('/^62\d{8,13}$/', $supplier->phone)) {
                throw new \Exception('Nomor telepon supplier tidak valid');
            }

            // Load necessary relations
            $purchaseOrder->load(['items.rawMaterial', 'creator']);

            // Prepare WhatsApp message
            $orderDateStr = $purchaseOrder->order_date ? $purchaseOrder->order_date->format('d/m/Y') : now()->format('d/m/Y');
            $createdAtStr = ($purchaseOrder->created_at ? $purchaseOrder->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i'));
            $requestedStr = $purchaseOrder->requested_delivery_date ? $purchaseOrder->requested_delivery_date->format('d/m/Y') : null;

            $message = "*PESANAN BAHAN MENTAH*\n";
            $message .= "No: {$purchaseOrder->order_number}\n";
            $message .= "Tanggal Pesanan: {$orderDateStr}\n";
            $message .= "Waktu Dibuat: {$createdAtStr}\n";
            if ($requestedStr) {
                $message .= "Pengiriman Diminta: {$requestedStr}\n";
            }
            $message .= "Supplier: {$supplier->name}\n\n";
            $message .= "*DETAIL PESANAN:*\n";

            $no = 1;
            foreach ($purchaseOrder->items as $item) {
                $message .= "{$no}. {$item->rawMaterial->name}\n";
                $message .= "   Jumlah: {$item->quantity} {$item->unit_name}\n";
                $message .= "   Harga: Rp " . number_format((float)$item->unit_price, 0, ',', '.') . "/" . $item->unit_name . "\n";
                if (!empty($item->notes)) {
                    $message .= "   Catatan: {$item->notes}\n";
                }
                $message .= "\n";
                $no++;
            }

            $message .= "*TOTAL: Rp " . number_format((float)$purchaseOrder->total_amount, 0, ',', '.') . "*\n\n";

            if (!empty($purchaseOrder->notes)) {
                $message .= "*Catatan:* {$purchaseOrder->notes}\n\n";
            }

            $message .= "Terima kasih.\n";
            $message .= "- " . ($purchaseOrder->creator ? $purchaseOrder->creator->name : 'Admin');

            // Send WhatsApp using Fonnte API
            $url = config('services.fonnte.api_url');
            $token = config('services.fonnte.token');

            if (empty($token)) {
                throw new \Exception('Fonnte API token tidak ditemukan');
            }

            $response = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post($url, [
                'target' => $supplier->phone,
                'message' => $message,
                'delay' => 1,
                'countryCode' => '62', // Indonesia
            ]);

            if ($response->successful()) {
                return true;
            } else {
                // Log the error but don't throw exception
                Log::error('Fonnte API error: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate and return the latest prices for selected raw materials
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateMaterialPrices(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'material_ids' => 'required|array',
                'material_ids.*' => 'required|exists:raw_materials,id',
            ]);

            $materialIds = $validated['material_ids'];

            // Get latest prices for the selected materials
            $materials = RawMaterial::with('unit')
                ->whereIn('id', $materialIds)
                ->get()
                ->map(function ($material) {
                    return [
                        'id' => $material->id,
                        'name' => $material->name,
                        'code' => $material->code,
                        'unit_name' => $material->unit->unit_name ?? '',
                        'unit_price' => $material->unit_price ?? 0,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Harga bahan mentah berhasil divalidasi',
                'materials' => $materials
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
