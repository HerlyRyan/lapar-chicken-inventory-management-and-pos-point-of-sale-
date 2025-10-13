<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

/**
 * Purchase Receipt Model
 * 
 * Used to track goods received from suppliers in response to purchase orders.
 * Status options: accepted, rejected, partial
 * Auto-generates receipt numbers.
 */
class PurchaseReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'purchase_order_id',
        'received_by',
        'receipt_date',
        'status',
        'notes',
        'receipt_photo',
        // Snapshot totals (server-calculated)
        'subtotal_items',
        'additional_cost_total',
        'discount_amount',
        'tax_amount',
        'total_amount',
    ];

    protected $casts = [
        'receipt_date' => 'date'
    ];

    // Status constants for better type safety
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PARTIAL = 'partial';

    /**
     * Boot method to auto-generate receipt number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseReceipt) {
            if (empty($purchaseReceipt->receipt_number)) {
                $purchaseReceipt->receipt_number = self::generateReceiptNumber();
            }
        });
    }

    /**
     * Generate unique receipt number in format: PR-YYYY-MM-XXX
     */
    private static function generateReceiptNumber(): string
    {
        $prefix = 'PR-' . date('Y-m-');
        $lastReceipt = self::where('receipt_number', 'like', $prefix . '%')
                          ->orderBy('receipt_number', 'desc')
                          ->first();

        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt->receipt_number, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Relationship with Purchase Order
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Relationship with Purchase Receipt Items
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseReceiptItem::class);
    }

    /**
     * Relationship with Purchase Receipt Additional Costs
     */
    public function additionalCosts(): HasMany
    {
        return $this->hasMany(PurchaseReceiptAdditionalCost::class);
    }

    /**
     * Relationship with User who received the goods
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Update raw material stock based on receipt items
     * 
     * @return bool Success status
     */
    public function updateStock(): bool
    {
        try {
            foreach ($this->items as $item) {
                $rawMaterial = $item->rawMaterial;
                
                // Only update stock for accepted or partially accepted items
                if ($item->item_status === 'accepted' || $item->item_status === 'partial') {
                    // Reuse domain helper to avoid duplicate logic
                    $rawMaterial->updateStock($item->received_quantity, 'in');
                }
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Determine overall receipt status from item statuses.
     * - All accepted => accepted
     * - All rejected => rejected
     * - Otherwise    => partial
     */
    public function determineStatusFromItems(): string
    {
        // Ensure items are loaded
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }

        $total = $this->items->count();
        if ($total === 0) {
            return self::STATUS_ACCEPTED; // default safe fallback
        }

        $accepted = $this->items->where('item_status', PurchaseReceiptItem::STATUS_ACCEPTED)->count();
        $rejected = $this->items->where('item_status', PurchaseReceiptItem::STATUS_REJECTED)->count();

        if ($accepted === $total) {
            return self::STATUS_ACCEPTED;
        }
        if ($rejected === $total) {
            return self::STATUS_REJECTED;
        }
        return self::STATUS_PARTIAL;
    }

    /**
     * Compute items total based on received quantities (no monetary exposure in messages).
     */
    public function computeItemsTotal(): float
    {
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        $sum = 0.0;
        foreach ($this->items as $item) {
            // Only accepted quantities count; rejected items have received_quantity = 0
            $sum += (float) $item->received_quantity * (float) $item->unit_price;
        }
        return round($sum, 2);
    }

    /**
     * Compute additional costs total.
     */
    public function computeAdditionalCostsTotal(): float
    {
        if (!$this->relationLoaded('additionalCosts')) {
            $this->load('additionalCosts');
        }
        $sum = 0.0;
        foreach ($this->additionalCosts as $cost) {
            $sum += (float) $cost->amount;
        }
        return round($sum, 2);
    }

    /**
     * Compute and optionally persist total payment.
     */
    public function computeTotalPayment(): float
    {
        // Prefer snapshot when available and non-null
        if (!is_null($this->getAttribute('total_amount'))) {
            return (float) $this->total_amount;
        }
        return $this->computeItemsTotal() + $this->computeAdditionalCostsTotal();
    }

    /**
     * Recalculate and persist snapshot totals on the receipt.
     * Uses server-side computation only. Discount and tax are optional and default to 0.
     */
    public function recalcAndSnapshotTotals(float $discount = 0.0, float $tax = 0.0): void
    {
        // Ensure relations are loaded for computation accuracy
        $this->loadMissing(['items', 'additionalCosts']);

        $itemsTotal = $this->computeItemsTotal();
        $addCostTotal = $this->computeAdditionalCostsTotal();
        $discount = round($discount, 2);
        $tax = round($tax, 2);
        $grandTotal = round($itemsTotal + $addCostTotal - $discount + $tax, 2);

        $this->subtotal_items = $itemsTotal;
        $this->additional_cost_total = $addCostTotal;
        $this->discount_amount = $discount;
        $this->tax_amount = $tax;
        $this->total_amount = $grandTotal;
        $this->save();
    }

    /**
     * Snapshot accepted quantities per raw material id (for stock adjustments).
     * Returns array [raw_material_id => qty]
     */
    public function getAcceptedQuantitiesSnapshot(): array
    {
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }
        $map = [];
        foreach ($this->items as $item) {
            if (in_array($item->item_status, [PurchaseReceiptItem::STATUS_ACCEPTED, PurchaseReceiptItem::STATUS_PARTIAL], true)) {
                $rmId = $item->raw_material_id;
                $map[$rmId] = ($map[$rmId] ?? 0) + (float) $item->received_quantity;
            }
        }
        return $map;
    }

    /**
     * Send WhatsApp notification to group about this receipt.
     * Does not include monetary values or supplier names.
     */
    public function sendWhatsAppNotificationToGroup(): bool
    {
        try {
            // Load relations
            $this->loadMissing(['purchaseOrder.supplier', 'items.rawMaterial.unit']);

            $token = config('services.fonnte.token');
            $apiUrl = config('services.fonnte.api_url');
            $groupId = config('services.fonnte.purchase_receipts_group_id', '120363417287107355@g.us');

            if (empty($token) || empty($apiUrl) || empty($groupId)) {
                return false;
            }

            $message = $this->buildWhatsAppGroupMessage();

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post($apiUrl, [
                'target' => $groupId,
                'message' => $message,
                'delay' => 0,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Failed to send receipt WhatsApp', [
                'receipt_id' => $this->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Build WhatsApp message for the group (privacy-safe).
     */
    private function buildWhatsAppGroupMessage(): string
    {
        $dateVal = $this->receipt_date;
        if ($dateVal instanceof \DateTimeInterface) {
            $dateStr = $dateVal->format('d/m/Y');
        } elseif (!empty($dateVal)) {
            $dateStr = Carbon::parse((string) $dateVal)->format('d/m/Y');
        } else {
            $dateStr = Carbon::now()->format('d/m/Y');
        }
        $supplierCode = optional($this->purchaseOrder->supplier)->code;
        $poNumber = optional($this->purchaseOrder)->order_number;

        $message = "📦 *PENERIMAAN BAHAN BAKU*\n";
        $message .= "==============================\n\n";
        $message .= "🧾 No. Penerimaan: {$this->receipt_number}\n";
        $message .= "📄 No. PO: {$poNumber}\n";
        $message .= "🏢 Kode Supplier: {$supplierCode}\n";
        $message .= "📅 Tanggal: {$dateStr}\n\n";

        $message .= "📋 *DETAIL ITEM*\n";
        $message .= "------------------------------\n";
        $i = 1;
        foreach ($this->items as $item) {
            $unit = optional($item->rawMaterial->unit)->name ?: ($item->purchaseOrderItem->unit_name ?? '') ;
            $message .= "{$i}. {$item->rawMaterial->name}\n";
            $message .= "   • Dipesan: {$item->ordered_quantity} {$unit}\n";
            $message .= "   • Diterima: {$item->received_quantity} {$unit}\n";
            $message .= "   • Ditolak: {$item->rejected_quantity} {$unit}\n";
            $message .= "   • Status: " . strtoupper($item->item_status) . "\n\n";
            $i++;
        }

        $message .= "==============================\n";
        $message .= "Status Penerimaan: *" . strtoupper($this->status) . "*\n";
        $message .= "(Pesan otomatis, tanpa informasi harga dan nama supplier)";

        return $message;
    }
}
