<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class RawMaterial extends Model
{
    use HasFactory;

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Supplier::class, 'supplier_id');
    }

    use HasFactory;

    protected $table = 'raw_materials';

    protected $fillable = [
        'name',
        'code',
        'category_id',
        'description',
        'image',
        'unit_id',
        'minimum_stock',
        'current_stock',
        'unit_price',
        'is_active',
        'supplier_id',
    ];


    protected $casts = [
        'minimum_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];


    /**
     * Get the category that owns the raw material.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }

    /**
     * Get the unit that owns the raw material.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get stock for any branch (raw materials are centralized).
     * Returns the current_stock from the raw_materials table.
     */
    public function getStockForBranch($branchId = null)
    {
        // Raw materials are centralized, so return current stock regardless of branch
        return (object) ['quantity' => $this->current_stock];
    }

    /**
     * Get current centralized stock
     */
    public function getCurrentStock()
    {
        return $this->current_stock;
    }

    /**
     * Scope for active raw materials.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isCentralized()
    {
        return $this->is_centralized;
    }

    public function isDecentralized()
    {
        return !$this->is_centralized;
    }

    public function canBeAccessedByBranch($branchId)
    {
        if ($this->isCentralized()) {
            // Centralized materials can be accessed by all branches
            return true;
        }
        
        // Decentralized materials can only be accessed by the managing branch
        return $this->managing_branch_id == $branchId;
    }

    public function getManagementTypeAttribute()
    {
        return $this->is_centralized ? 'Terpusat' : 'Tidak Terpusat';
    }

    public function getManagementBadgeAttribute()
    {
        if ($this->is_centralized) {
            return '<span class="badge bg-success">Terpusat</span>';
        } else {
            return '<span class="badge bg-warning">Tidak Terpusat - ' . ($this->managingBranch->name ?? 'N/A') . '</span>';
        }
    }

    // Helper methods
    public function isLowStock()
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function updateStock($quantity, $type = 'in')
    {
        // Convert to string to ensure proper decimal handling
        $currentStock = (string)(float)$this->current_stock;
        $quantityValue = (string)(float)$quantity;
        
        if ($type === 'in') {
            $this->attributes['current_stock'] = (string)((float)$currentStock + (float)$quantityValue);
        } else {
            $this->attributes['current_stock'] = (string)((float)$currentStock - (float)$quantityValue);
        }
        $this->save();
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock');
    }
}
