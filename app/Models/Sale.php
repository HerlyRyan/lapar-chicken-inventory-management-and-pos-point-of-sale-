<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sale_number',
        'sale_code',
        'branch_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'subtotal_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'final_amount',
        'payment_method',
        'paid_amount',
        'change_amount',
        'status'
    ];
    
    protected $casts = [
        'subtotal_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];
    
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Compatibility accessor: some views expect total_amount
     * Map it to final_amount to avoid changing many blades.
     */
    public function getTotalAmountAttribute()
    {
        return $this->final_amount;
    }
}
