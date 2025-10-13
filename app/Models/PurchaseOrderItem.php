<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Purchase Order Item Model
 * 
 * Model for purchase order items aligned with SQL database structure.
 */
class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'raw_material_id',
        'unit_id',
        'unit_name',
        'quantity',
        'unit_price',
        'total_price',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    /**
     * Boot method to auto-calculate total price
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-calculate total price when creating or updating
        static::saving(function ($item) {
            $item->calculateTotalPrice();
        });
    }

    /**
     * Relationship with Purchase Order
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Relationship with Raw Material
     */
    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    /**
     * Calculate and set total price based on quantity and unit price
     */
    private function calculateTotalPrice(): void
    {
        $this->total_price = $this->quantity * $this->unit_price;
    }

    /**
     * Get formatted unit price
     * 
     * @return string
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->unit_price, 0, ',', '.');
    }

    /**
     * Get formatted total price
     * 
     * @return string
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->total_price, 0, ',', '.');
    }

    /**
     * Validate that item has all required fields
     * 
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->raw_material_id && 
               $this->quantity > 0 && 
               $this->unit_price > 0;
    }
}
