<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'sale_id',
        'item_type',
        'item_id',
        'item_name',
        'quantity',
        'unit_price',
        'subtotal'
    ];
    
    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];
    
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }
}
