<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Purchase Order Model
 * 
 * Model for purchase orders aligned with SQL database structure.
 * Status options: draft, ordered, received, partially_received, rejected
 * Auto-generates order numbers and codes with WhatsApp integration.
 */
class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_code',
        'supplier_id',
        'created_by',
        'order_date',
        'requested_delivery_date',
        'status',
        'notes',
        'total_amount',
        'ordered_at',
        'whatsapp_sent'
    ];

    protected $casts = [
        'order_date' => 'date',
        'requested_delivery_date' => 'date',
        'ordered_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'whatsapp_sent' => 'boolean'
    ];

    // Status constants matching SQL enum
    const STATUS_DRAFT = 'draft';
    const STATUS_ORDERED = 'ordered';
    const STATUS_RECEIVED = 'received';
    const STATUS_PARTIALLY_RECEIVED = 'partially_received';
    const STATUS_REJECTED = 'rejected';

    /**
     * Relationship with Supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Relationship with Purchase Order Items
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Relationship with User who created the order
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with Purchase Receipt
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(PurchaseReceipt::class);
    }

    /**
     * Boot method to auto-generate order number, code, and date
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchaseOrder) {
            if (empty($purchaseOrder->order_number)) {
                $purchaseOrder->order_number = self::generateOrderNumber();
            }
            if (empty($purchaseOrder->order_code)) {
                $purchaseOrder->order_code = self::generateOrderCode();
            }
            if (empty($purchaseOrder->order_date)) {
                $purchaseOrder->order_date = now()->toDateString();
            }
        });
    }

    /**
     * Generate unique order number in format: PO-YYYY-MM-XXX
     */
    private static function generateOrderNumber(): string
    {
        $prefix = 'PO-' . date('Y-m-');
        $lastOrder = self::where('order_number', 'like', $prefix . '%')
                        ->orderBy('order_number', 'desc')
                        ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique order code in format: POXXX
     */
    private static function generateOrderCode(): string
    {
        $lastOrder = self::orderBy('id', 'desc')->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        
        return 'PO' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Update status to ordered and send WhatsApp notification
     * 
     * @param bool $sendWhatsApp Whether to send WhatsApp notification
     * @return bool Success status
     */
    public function markAsOrdered(bool $sendWhatsApp = false): bool
    {
        try {
            $this->status = self::STATUS_ORDERED;
            $this->save();
            
            if ($sendWhatsApp) {
                $this->sendWhatsAppNotification();
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Send WhatsApp notification to supplier
     * 
     * @return bool Success status
     */
    public function sendWhatsAppNotification(): bool
    {
        // Load related data if not already loaded
        if (!$this->relationLoaded('supplier') || !$this->relationLoaded('items')) {
            $this->load(['supplier', 'items.rawMaterial.unit']);
        }
        
        // Check if supplier has phone number
        if (empty($this->supplier->phone)) {
            return false;
        }
        
        // Build WhatsApp message
        $message = $this->buildWhatsAppMessage();
        
        try {
            // Get Fonnte API token and URL from config
            $token = config('services.fonnte.token');
            $apiUrl = config('services.fonnte.api_url');
            
            if (empty($token) || empty($apiUrl)) {
                return false;
            }
            
            // Send WhatsApp message using Fonnte API
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->post($apiUrl, [
                'target' => $this->supplier->phone,
                'message' => $message,
                'delay' => 0,
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Build WhatsApp message for supplier (excludes supplier name and prices for security)
     * 
     * @return string Formatted message
     */
    private function buildWhatsAppMessage(): string
    {
        // Header with emoji and order details
        $message = "🛒 *PESANAN BAHAN MENTAH*\n";
        $message .= "==============================\n\n";
        $message .= "📝 *Nomor Pesanan:* {$this->order_number}\n";
        $message .= "🏢 *Kode Supplier:* {$this->supplier->code}\n";
        $message .= "📅 *Tanggal:* " . ($this->created_at ? $this->created_at->format('d/m/Y') : Carbon::now()->format('d/m/Y')) . "\n";
        $message .= "\n";
        
        // Item details (without prices for security)
        $message .= "📋 *DETAIL PESANAN:*\n";
        $message .= "------------------------------\n";
        
        $i = 1;
        foreach ($this->items as $item) {
            $message .= "{$i}. {$item->rawMaterial->name}\n";
            $message .= "   • Jumlah: {$item->quantity} {$item->unit_name}\n";
            if (!empty($item->notes)) {
                $message .= "   • Catatan: {$item->notes}\n";
            }
            $message .= "\n";
            $i++;
        }
        
        // Footer without total amount for security
        $message .= "==============================\n";
        
        if (!empty($this->notes)) {
            $message .= "📝 *Catatan:*\n{$this->notes}\n\n";
        }
        
        $message .= "✅ Mohon konfirmasi pesanan ini secepatnya. Terima kasih! 🙏";
        
        return $message;
    }

    /**
     * Calculate total amount from all items
     * 
     * @return string Total amount
     */
    public function calculateTotalAmount()
    {
        // Return as string for proper decimal casting
        return (string) $this->items->sum('total_price');
    }

    /**
     * Update total amount based on current items
     */
    public function updateTotalAmount(): void
    {
        // Using direct DB assignment to avoid mutator/casting issues
        $total = $this->calculateTotalAmount();
        $this->attributes['total_amount'] = $total;
        $this->save();
    }

    /**
     * Check if order can be edited (only draft status)
     * 
     * @return bool
     */
    public function canBeEdited(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Determine if the purchase order can be ordered (sent to supplier via WhatsApp)
     *
     * Business rules:
     * - Status must be draft
     * - Must have at least 1 item
     * - Supplier must have a valid WhatsApp phone number (format: 62xxxxxxxx)
     */
    public function canBeOrdered(): bool
    {
        // Must be in draft status
        if ($this->status !== self::STATUS_DRAFT) {
            return false;
        }

        // Ensure relations are available
        if (!$this->relationLoaded('supplier')) {
            $this->load('supplier');
        }
        if (!$this->relationLoaded('items')) {
            $this->load('items');
        }

        // Must have at least one item
        if ($this->items->isEmpty()) {
            return false;
        }

        // Supplier must have valid phone number for WhatsApp (62 + 8-13 digits)
        $phone = $this->supplier->phone ?? null;
        if (empty($phone) || !preg_match('/^62\d{8,13}$/', $phone)) {
            return false;
        }

        return true;
    }

    /**
     * Sync purchase order status based on its related purchase receipt.
     * Rules:
     * - accepted  => received
     * - partial   => partially_received
     * - rejected  => rejected
     * - no receipt => ordered if ordered_at set, else draft
     */
    public function syncStatusFromReceipt(): void
    {
        // Ensure the latest receipt relation state
        if (!$this->relationLoaded('receipt')) {
            $this->load('receipt');
        }

        $newStatus = $this->status;

        $receipt = $this->receipt;
        if ($receipt) {
            switch ($receipt->status) {
                case PurchaseReceipt::STATUS_ACCEPTED:
                    $newStatus = self::STATUS_RECEIVED;
                    break;
                case PurchaseReceipt::STATUS_PARTIAL:
                    $newStatus = self::STATUS_PARTIALLY_RECEIVED;
                    break;
                case PurchaseReceipt::STATUS_REJECTED:
                    $newStatus = self::STATUS_REJECTED;
                    break;
            }
        } else {
            // If receipt was removed or doesn't exist, fallback to order lifecycle
            $newStatus = $this->ordered_at ? self::STATUS_ORDERED : self::STATUS_DRAFT;
        }

        if ($newStatus !== $this->status) {
            $this->status = $newStatus;
            $this->save();
        }
    }
}
