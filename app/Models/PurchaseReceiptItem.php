<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Purchase Receipt Item Model
 * 
 * Represents individual items within a purchase receipt.
 * Tracks ordered vs received quantities and item status.
 */
class PurchaseReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_receipt_id',
        'purchase_order_item_id',
        'raw_material_id',
        'ordered_quantity',
        'received_quantity',
        'rejected_quantity',
        'unit_price',
        'item_status',
        'condition_photo',
        'notes'
    ];

    protected $casts = [
        'ordered_quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
        'rejected_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2'
    ];

    // Status constants
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PARTIAL = 'partial';

    /**
     * Relationship with Purchase Receipt
     */
    public function purchaseReceipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class);
    }

    /**
     * Relationship with Purchase Order Item
     */
    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    /**
     * Relationship with Raw Material
     */
    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    /**
     * Calculate total value for this item
     */
    public function getTotalValueAttribute(): float
    {
        return $this->received_quantity * $this->unit_price;
    }

    /**
     * Check if item is fully accepted
     */
    public function isFullyAccepted(): bool
    {
        return $this->item_status === self::STATUS_ACCEPTED && 
               $this->received_quantity == $this->ordered_quantity;
    }

    /**
     * Check if item is fully rejected
     */
    public function isFullyRejected(): bool
    {
        return $this->item_status === self::STATUS_REJECTED ||
               $this->received_quantity == 0;
    }

    /**
     * Check if item is partially accepted
     */
    public function isPartiallyAccepted(): bool
    {
        return $this->item_status === self::STATUS_PARTIAL ||
               ($this->received_quantity > 0 && $this->received_quantity < $this->ordered_quantity);
    }
}
