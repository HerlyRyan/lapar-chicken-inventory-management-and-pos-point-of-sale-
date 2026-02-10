<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SemiFinishedProduct extends Model
{
    use HasFactory;

    protected $table = 'semi_finished_products';

    protected $fillable = [
        'name',
        'code',
        'description',
        'category_id',
        'unit_id',
        'production_cost',
        'selling_price',
        'minimum_stock',
        'current_stock',
        'is_active',
        'is_centralized',
        'managing_branch_id',
        'image',
    ];

    protected $casts = [
        'production_cost' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'is_active' => 'boolean',
        'is_centralized' => 'boolean',
    ];

    /**
     * Get the category that owns the semi-finished product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the unit that owns the semi-finished product.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the managing branch.
     */
    public function managingBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'managing_branch_id');
    }

    /**
     * Get all semi-finished branch stocks for this product.
     */
    public function semiFinishedBranchStocks()
    {
        return $this->hasMany(SemiFinishedBranchStock::class);
    }


    // Branch Stock Methods
    public function getStockForBranch($branchId)
    {
        return $this->semiFinishedBranchStocks()->where('branch_id', $branchId)->first();
    }

    public function getCurrentStockForBranch($branchId)
    {
        $branchStock = $this->getStockForBranch($branchId);
        return $branchStock ? $branchStock->quantity : 0;
    }

    public function initializeStockForBranch($branchId, $initialStock = 0)
    {
        // No need to set average_cost as we're using product-level pricing (production_cost)
        // We'll set it to 0 as it's not used anymore
        return SemiFinishedBranchStock::firstOrCreate(
            [
                'branch_id' => $branchId,
                'semi_finished_product_id' => $this->id
            ],
            [
                'quantity' => $initialStock
            ]
        );
    }

    public function updateStockForBranch($branchId, $quantity, $cost = null, $operation = 'set')
    {
        $branchStock = $this->getStockForBranch($branchId);

        if (!$branchStock) {
            $branchStock = $this->initializeStockForBranch($branchId);
        }

        // Always pass null for cost since we're using product-level pricing
        // The cost parameter is kept for backward compatibility but ignored
        return $branchStock->updateStock($quantity, null, $operation);
    }

    public function getTotalStockAcrossBranches()
    {
        return $this->semiFinishedBranchStocks()->sum('quantity');
    }

    public function getLowStockBranches()
    {
        return $this->semiFinishedBranchStocks()->where('quantity', '<', 'minimum_stock')->with('branch')->get();
    }

    // Legacy support methods (for backward compatibility)
    public function getCurrentStockAttribute()
    {
        // 1) Prefer explicit current branch context (set by BranchContext middleware, incl. ?branch_id)
        if (app()->bound('current_branch_id') && app('current_branch_id')) {
            return $this->getCurrentStockForBranch(app('current_branch_id'));
        }

        // 2) Fallback: authenticated user's own branch
        if (auth()->check() && auth()->user() && auth()->user()->branch_id) {
            return $this->getCurrentStockForBranch(auth()->user()->branch_id);
        }

        // 3) Otherwise, return total stock across all branches
        return $this->getTotalStockAcrossBranches();
    }

    public function getMinimumStockAttribute()
    {
        // Always return product-level minimum_stock as branch-level has been removed
        return $this->attributes['minimum_stock'] ?? 0;
    }

    public function isLowStock($branchId = null)
    {
        $minimum_stock = $this->attributes['minimum_stock'] ?? 0;

        if ($branchId) {
            $currentStock = $this->getCurrentStockForBranch($branchId);
            return $currentStock <= $minimum_stock;
        }

        // Check if total stock across branches is low
        $totalStock = $this->getTotalStockAcrossBranches();
        return $totalStock <= $minimum_stock;
    }

    // Replace legacy unit attribute
    public function getUnitAttribute()
    {
        // Use relation property if loaded, fallback to query if not
        if (array_key_exists('unit', $this->relations)) {
            return $this->relations['unit'] ? $this->relations['unit']->unit_name : null;
        }
        // fallback: query if not eager loaded
        $unit = $this->unit()->first();
        return $unit ? $unit->unit_name : null;
    }

    // Legacy alias: map unit_price access to production_cost
    public function getUnitPriceAttribute()
    {
        return $this->attributes['production_cost'] ?? 0;
    }

    public function setUnitPriceAttribute($value)
    {
        $this->attributes['production_cost'] = $value;
    }

    // Legacy methods updated for multi-branch
    public function updateStock($quantity, $type = 'in', $branchId = null)
    {
        // Get branch ID from context if not provided
        if (!$branchId && auth()->check() && auth()->user()) {
            $branchId = auth()->user()->branch_id;
        }

        if (!$branchId) {
            throw new \Exception('Branch ID required for stock update');
        }

        $operation = $type === 'in' ? 'add' : 'reduce';
        return $this->updateStockForBranch($branchId, $quantity, null, $operation);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query, $branchId = null)
    {
        if ($branchId) {
            return $query->whereHas('semiFinishedBranchStocks', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->where('quantity', '<', 'minimum_stock');
            });
        }

        return $query->whereHas('semiFinishedBranchStocks', function ($q) {
            $q->where('quantity', '<', 'minimum_stock');
        });
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->whereHas('semiFinishedBranchStocks', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        });
    }
}
