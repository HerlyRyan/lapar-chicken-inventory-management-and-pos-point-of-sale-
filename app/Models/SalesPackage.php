<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'category_id',
        'base_price',
        'discount_amount',
        'discount_percentage',
        'additional_charge',
        'final_price',
        'image',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'additional_charge' => 'decimal:2',
        'final_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get the package items (recipe components)
     */
    public function packageItems()
    {
        return $this->hasMany(SalesPackageItem::class);
    }

    /**
     * Get the creator of this package
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the category of this package
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Calculate and update final price based on base price, discount, and additional charges
     */
    public function calculateFinalPrice()
    {
        $discountAmount = 0;
        
        // Calculate discount amount
        if ($this->discount_percentage > 0) {
            $discountAmount = ($this->base_price * $this->discount_percentage) / 100;
        } else {
            $discountAmount = $this->discount_amount;
        }
        
        $this->final_price = $this->base_price - $discountAmount + $this->additional_charge;
        return $this->final_price;
    }

    /**
     * Calculate base price from package items
     */
    public function calculateBasePrice()
    {
        $this->base_price = $this->packageItems()->sum('total_price');
        return $this->base_price;
    }

    /**
     * Check if package is available in specific branch based on component stock
     */
    public function isAvailableInBranch($branchId)
    {
        foreach ($this->packageItems as $item) {
            $branchStock = $item->finishedProduct->finishedBranchStocks()
                ->where('branch_id', $branchId)
                ->first();
            
            if (!$branchStock || $branchStock->quantity < $item->quantity) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get available quantity in specific branch (minimum available based on recipe)
     */
    public function getAvailableQuantityInBranch($branchId)
    {
        $minAvailable = PHP_INT_MAX;
        
        foreach ($this->packageItems as $item) {
            $branchStock = $item->finishedProduct->finishedBranchStocks()
                ->where('branch_id', $branchId)
                ->first();
            
            $availableForThisItem = $branchStock ? 
                floor($branchStock->quantity / $item->quantity) : 0;
            
            $minAvailable = min($minAvailable, $availableForThisItem);
        }
        
        return $minAvailable === PHP_INT_MAX ? 0 : $minAvailable;
    }

    /**
     * Generate unique package code
     */
    public static function generateCode()
    {
        do {
            $code = 'PKG-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
        } while (self::where('code', $code)->exists());
        
        return $code;
    }
}
