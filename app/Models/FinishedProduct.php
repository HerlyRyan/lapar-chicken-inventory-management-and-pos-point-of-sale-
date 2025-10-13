<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Category;
use App\Models\SaleItem;

class FinishedProduct extends Model
{
    use HasFactory;

    protected $table = 'finished_products';

    protected $fillable = [
        'name',
        'code',
        'description',
        'category_id',
        'unit_id',
        'price',           // Main price field in database
        'minimum_stock',       // Minimum stock field
        'photo',           // Product photo
        'production_cost', // Production cost
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'production_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Accessor for selling_price (alias for price)
    public function getSellingPriceAttribute()
    {
        return $this->price;
    }

    // minimum_stock is already a database column, no accessor needed

    /**
     * Get the category that owns the finished product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the unit that owns the finished product.
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
     * Get all finished branch stocks for this finished product.
     */
    public function finishedBranchStocks()
    {
        return $this->hasMany(FinishedBranchStock::class);
    }

    // Accessor untuk backward compatibility - removed due to circular reference
    // minimum_stock is already a database column, no accessor needed

    // getCurrentStockAttribute removed due to circular reference
    // current_stock is already a database column, no accessor needed
    
    public function getUnitPriceAttribute()
    {
        return $this->selling_price;
    }

    // Helper methods
    public function isLowStock()
    {
        return $this->stock_quantity <= $this->min_stock;
    }

    public function updateStock($quantity, $type = 'in')
    {
        if ($type === 'in') {
            $this->increment('stock_quantity', $quantity);
        } else {
            $this->decrement('stock_quantity', $quantity);
        }
    }

    public function canSell($quantity)
    {
        return $this->stock_quantity >= $quantity;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock');
    }



    public function getStockForBranch($branchId)
    {
        return $this->finishedBranchStocks()->where('branch_id', $branchId)->first();
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Initialize stock for all active branches when product is created
     */
    public function initializeStockForAllBranches()
    {
        $branches = Branch::where('is_active', true)->get();
        
        foreach ($branches as $branch) {
            $this->initializeStockForBranch($branch->id);
        }
    }

    /**
     * Initialize stock for a specific branch
     * PROTECTION: Prevent stock initialization for production centers
     */
    public function initializeStockForBranch($branchId, $quantity = 0, $minimumStock = 0)
    {
        // Check if branch is production center
        $branch = Branch::find($branchId);
        if ($branch && $branch->type === 'production') {
            \Log::warning('Attempted to initialize finished product stock for production center', [
                'finished_product_id' => $this->id,
                'branch_id' => $branchId,
                'branch_name' => $branch->name
            ]);
            return false; // Don't initialize stock for production centers
        }
        // Minimum stock is now stored only in the finished_products table, not in branch stocks
        
        FinishedBranchStock::firstOrCreate(
            [
                'branch_id' => $branchId,
                'finished_product_id' => $this->id,
            ],
            [
                'quantity' => $quantity,
                
            ]
        );
    }

    /**
     * Update stock for a specific branch
     */
    public function updateStockForBranch($branchId, $type, $quantity, $notes = null, $userId = null)
    {
        $branchStock = $this->getStockForBranch($branchId);
        
        if (!$branchStock) {
            $this->initializeStockForBranch($branchId);
            $branchStock = $this->getStockForBranch($branchId);
        }
        
        return $branchStock->updateStock($type, $quantity, $notes, $userId);
    }
}