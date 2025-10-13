<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesPackageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_package_id',
        'finished_product_id',
        'quantity',
        'unit_price',
        'total_price'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2'
    ];

    /**
     * Get the sales package this item belongs to
     */
    public function salesPackage()
    {
        return $this->belongsTo(SalesPackage::class);
    }

    /**
     * Get the finished product
     */
    public function finishedProduct()
    {
        return $this->belongsTo(FinishedProduct::class);
    }

    /**
     * Calculate total price when quantity or unit price changes
     */
    public function calculateTotalPrice()
    {
        $this->total_price = (float)$this->quantity * (float)$this->unit_price;
        return $this->total_price;
    }
}
