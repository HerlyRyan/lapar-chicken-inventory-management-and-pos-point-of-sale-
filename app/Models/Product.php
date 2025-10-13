<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    /**
     * This is a wrapper model for FinishedProduct to maintain compatibility with the sales module.
     * It redirects all queries to the FinishedProduct model.
     */

    protected $table = 'finished_products';

    protected $fillable = [
        'name',
        'code',
        'description',
        'category_id',
        'unit_id',
        'price',
        'minimum_stock',
        'photo',
        'production_cost',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'production_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    /**
     * The "booted" method of the model.
     * This ensures we never try to query a non-existent 'stock' column
     */
    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('is_active', true);
        });
    }

    /**
     * Get the stock attribute (for compatibility with sales module)
     * This uses the finished branch stock relationship
     */
    public function getStockAttribute()
    {
        // Get the current branch ID from session or default to 1
        $branchId = session('branch_id', 1);
        
        // Find the branch stock for this product
        $branchStock = $this->finishedBranchStocks()
            ->where('branch_id', $branchId)
            ->first();
            
        return $branchStock ? $branchStock->quantity : 0;
    }

    /**
     * Get the finished branch stocks for this product
     */
    public function finishedBranchStocks()
    {
        return $this->hasMany(FinishedBranchStock::class, 'finished_product_id');
    }

    /**
     * Get the category that owns the product
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the unit that owns the product
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
