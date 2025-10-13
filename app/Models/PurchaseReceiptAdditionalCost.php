<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Purchase Receipt Additional Cost Model
 * 
 * Represents additional costs associated with a purchase receipt
 * such as shipping, handling, customs, etc.
 */
class PurchaseReceiptAdditionalCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_receipt_id',
        'cost_name',
        'amount',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    /**
     * Relationship with Purchase Receipt
     */
    public function purchaseReceipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class);
    }
}
