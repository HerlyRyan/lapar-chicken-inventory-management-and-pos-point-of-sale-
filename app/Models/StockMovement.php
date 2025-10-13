<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type',
        'item_id',
        'material_id',
        'finished_product_id',
        'semi_finished_product_id',
        'branch_id',
        'type',
        'movement_category',
        'quantity',
        'quantity_before',
        'quantity_moved',
        'quantity_after',
        'unit_cost',
        'total_cost',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
        'processed_by',
        'processed_at'
    ];

    protected $casts = [
        'quantity_before' => 'decimal:2',
        'quantity_moved' => 'decimal:2',
        'quantity_after' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'processed_at' => 'datetime'
    ];

    public function stockable()
    {
        return $this->morphTo(__FUNCTION__, 'item_type', 'item_id');
    }

    // Relationships
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function finishedProduct()
    {
        return $this->belongsTo(FinishedProduct::class);
    }

    public function semiFinishedProduct()
    {
        return $this->belongsTo(SemiFinishedProduct::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Accessors
    public function getItemNameAttribute()
    {
        if ($this->material) {
            return $this->material->name;
        } elseif ($this->finishedProduct) {
            return $this->finishedProduct->name;
        } elseif ($this->semiFinishedProduct) {
            return $this->semiFinishedProduct->name;
        }
        return 'Unknown Item';
    }

    public function getItemTypeAttribute()
    {
        if ($this->material_id) {
            return 'material';
        } elseif ($this->finished_product_id) {
            return 'finished_product';
        } elseif ($this->semi_finished_product_id) {
            return 'semi_finished_product';
        }
        return null;
    }

    // Scopes
    public function scopeByMovementType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('movement_category', $category);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeRawMaterials($query)
    {
        return $query->whereNotNull('material_id');
    }

    public function scopeSemiFinishedProducts($query)
    {
        return $query->whereNotNull('semi_finished_product_id');
    }

    public function scopeFinishedProducts($query)
    {
        return $query->whereNotNull('finished_product_id');
    }
}
