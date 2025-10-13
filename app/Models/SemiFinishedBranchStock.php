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
        'quantity' => 'decimal:2',
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
    public function updateStock($quantity, $cost = null, $operation = 'set')
    {
        $oldStock = $this->quantity;
        
        switch ($operation) {
            case 'add':
                $this->quantity += $quantity;
                break;
            case 'subtract':
                $this->quantity = max(0, $this->quantity - $quantity);
                break;
            case 'set':
            default:
                $this->quantity = max(0, $quantity);
                break;
        }

        // No longer updating average_cost as we're using product-level pricing
        // The cost parameter is ignored as prices are standardized across branches

        $this->last_updated = now();
        return $this->save();
    }

    /**
     * Bridge accessor for views expecting `$stock->stockable` (returns SemiFinishedProduct)
     */
    public function getStockableAttribute()
    {
        return $this->semiFinishedProduct;
    }
}
