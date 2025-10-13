<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedBranchStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'finished_product_id',
        'quantity',
        'minimum_stock',
        
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        
    ];

    /**
     * Get the branch that owns the stock.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the finished product that owns the stock.
     */
    public function finishedProduct()
    {
        return $this->belongsTo(FinishedProduct::class);
    }

    /**
     * Update stock quantity with movement tracking
     */
    public function updateStock($type, $quantity, $notes = null, $userId = null)
    {
        $oldStock = $this->quantity;
        
        switch ($type) {
            case 'in':
                $this->quantity += $quantity;
                break;
            case 'out':
                $this->quantity -= $quantity;
                break;
            case 'return':
                $this->quantity += $quantity;
                break;
            default:
                throw new \InvalidArgumentException("Invalid stock movement type: {$type}");
        }

        // Ensure stock doesn't go negative
        if ($this->quantity < 0) {
            throw new \Exception("Stock cannot be negative. Current stock: {$oldStock}, Requested: {$quantity}");
        }

        $this->save();

        // Create stock movement record using existing columns
        StockMovement::create([
            'branch_id' => $this->branch_id,
            'finished_product_id' => $this->finished_product_id,
            'type' => $type,
            'quantity_before' => $oldStock,
            'quantity_moved' => $quantity,
            'quantity_after' => $this->quantity,
            'notes' => $notes,
            'created_by' => $userId,
        ]);

        return $this;
    }

    /**
     * Check if stock is below minimum
     */
    public function isBelowMinimum()
    {
        return $this->minimum_stock && $this->quantity < $this->minimum_stock;
    }

    /**
     * Get stock status
     */
    public function getStockStatus()
    {
        if ($this->quantity <= 0) {
            return 'out_of_stock';
        } elseif ($this->isBelowMinimum()) {
            return 'low_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Scope for filtering by branch
     */
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope for filtering by finished product
     */
    public function scopeForFinishedProduct($query, $finishedProductId)
    {
        return $query->where('finished_product_id', $finishedProductId);
    }

    /**
     * Bridge accessor for views expecting `$stock->stockable` (returns FinishedProduct)
     */
    public function getStockableAttribute()
    {
        return $this->finishedProduct;
    }
}
