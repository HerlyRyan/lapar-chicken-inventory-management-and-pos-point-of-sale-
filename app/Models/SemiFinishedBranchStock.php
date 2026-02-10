<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemiFinishedBranchStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'semi_finished_product_id',
        'quantity',
        'maximum_stock',
        'last_updated'
    ];

    protected $casts = [
        'quantity' => 'float',
        'maximum_stock' => 'decimal:2',
        'last_updated' => 'datetime'
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function semiFinishedProduct()
    {
        return $this->belongsTo(SemiFinishedProduct::class);
    }

    // Accessors
    public function getIsLowStockAttribute()
    {
        $productMinimumStock = $this->semiFinishedProduct->minimum_stock ?? 0;
        return $productMinimumStock > 0 && $this->quantity <= $productMinimumStock;
    }

    public function getStockStatusAttribute()
    {
        if ($this->is_low_stock) {
            return 'low';
        } elseif ($this->maximum_stock && $this->quantity >= $this->maximum_stock) {
            return 'high';
        }
        return 'normal';
    }

    public function getStockValueAttribute()
    {
        // Use product-level unit_price instead of branch-specific average_cost
        return $this->quantity * ($this->semiFinishedProduct->unit_price ?? 0);
    }

    // Scopes
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= minimum_stock')
            ->whereNotNull('minimum_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', '<=', 0);
    }

    // Methods
    public function updateStock(float $quantity, $refId = null, string $mode = 'set'): void
    {
        match ($mode) {
            'add'      => $this->quantity += $quantity,
            'reduce' => $this->quantity -= $quantity,
            'set'      => $this->quantity = (float) $quantity,
            default    => throw new \InvalidArgumentException('Invalid stock mode'),
        };

        $this->save();
    }

    public function setStock(float $quantity): void
    {
        $this->quantity = $quantity;
        $this->save();
    }

    /**
     * Bridge accessor for views expecting `$stock->stockable` (returns SemiFinishedProduct)
     */
    public function getStockableAttribute()
    {
        return $this->semiFinishedProduct;
    }
}
