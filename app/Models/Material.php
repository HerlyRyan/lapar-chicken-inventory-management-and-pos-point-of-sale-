<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Deprecated: Use RawMaterial instead.
class Material extends Model
{
    // Deprecated. Use RawMaterial model instead.
    const CATEGORY_RAW = 'raw_material';           // Bahan Mentah
    const CATEGORY_SEMI = 'semi_finished';         // Bahan Setengah Jadi
    const CATEGORY_FINISHED = 'finished_product';  // Produk Siap Jual

    // Relationships
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function managingBranch()
    {
        return $this->belongsTo(Branch::class, 'managing_branch_id');
    }

    public function branchStocks()
    {
        return $this->hasMany(BranchStock::class, 'item_id')->where('item_type', 'material');
    }

    public function getStockForBranch($branchId)
    {
        return $this->branchStocks()->where('branch_id', $branchId)->first();
    }

    // Helper methods untuk kategori
    public function getCategoryLabel()
    {
        return match($this->category) {
            self::CATEGORY_RAW => 'Bahan Mentah',
            self::CATEGORY_SEMI => 'Bahan Setengah Jadi',
            self::CATEGORY_FINISHED => 'Produk Siap Jual',
            default => 'Tidak diketahui'
        };
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->minimum_stock;
    }

    public function updateStock($quantity, $type = 'in')
    {
        if ($type === 'in') {
            $this->quantity += $quantity;
        } else {
            $this->quantity -= $quantity;
        }
        $this->save();
    }

    // Scopes
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeRawMaterials($query)
    {
        return $query->where('category', self::CATEGORY_RAW);
    }

    public function scopeSemiFinished($query)
    {
        return $query->where('category', self::CATEGORY_SEMI);
    }

    public function scopeFinishedProducts($query)
    {
        return $query->where('category', self::CATEGORY_FINISHED);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getStockLevelAttribute()
    {
        if ($this->quantity <= $this->minimum_stock) {
            return 'low';
        } elseif ($this->quantity <= $this->minimum_stock * 1.5) {
            return 'medium';
        }
        return 'high';
    }

    // Helper method for image URL or default
    public function getImageUrl()
    {
        return $this->image ? asset($this->image) : null;
    }

    // Helper method for default icon
    public function getDefaultIcon()
    {
        return 'bi-box-seam';
    }
}
