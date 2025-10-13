<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Represents a Semi-Finished Usage Request Item (PRIMARY model).
 *
 * Table: semi_finished_usage_request_items
 *
 * Relationships:
 * - request(): belongsTo SemiFinishedUsageRequest via semi_finished_request_id
 * - semiFinishedProduct(): belongsTo SemiFinishedProduct via semi_finished_product_id
 * - rawMaterial(): legacy alias via raw_material_id for backward compatibility
 * - unit(): belongsTo Unit via unit_id
 */
class SemiFinishedUsageRequestItem extends Model
{
    use HasFactory;

    /**
     * Underlying table name
     */
    protected $table = 'semi_finished_usage_request_items';

    protected $fillable = [
        'semi_finished_request_id',
        'raw_material_id',
        'semi_finished_product_id',
        'quantity',
        'unit_id',
        'unit_price',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
    ];

    /**
     * Parent request (primary relation)
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(SemiFinishedUsageRequest::class, 'semi_finished_request_id');
    }

    /**
     * Legacy raw material relation (kept for backward compatibility)
     */
    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class, 'raw_material_id');
    }

    /**
     * Semi-finished product relation
     */
    public function semiFinishedProduct(): BelongsTo
    {
        return $this->belongsTo(SemiFinishedProduct::class, 'semi_finished_product_id');
    }

    /**
     * Unit relation
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Computed subtotal
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Formatted subtotal
     */
    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format((float)$this->subtotal, 0, ',', '.');
    }

    /**
     * Formatted unit price
     */
    public function getFormattedUnitPriceAttribute()
    {
        return 'Rp ' . number_format((float)$this->unit_price, 0, ',', '.');
    }

    /**
     * Formatted quantity with unit
     */
    public function getQuantityWithUnitAttribute()
    {
        return $this->quantity . ' ' . ($this->unit->symbol ?? $this->unit->name ?? '');
    }
}
