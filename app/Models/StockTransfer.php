<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Branch;
use App\Models\User;
use App\Models\FinishedProduct;
use App\Models\SemiFinishedProduct;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_type',           // 'finished' | 'semi-finished'
        'item_id',
        'from_branch_id',
        'to_branch_id',
        'quantity',
        'notes',
        'status',              // 'sent' | 'accepted' | 'rejected'
        'sent_by',
        'handled_by',
        'handled_at',
        'response_notes',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    // Relationships
    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function sentByUser()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function handledByUser()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Related FinishedProduct when item_type === 'finished'
     */
    public function finishedProduct()
    {
        return $this->belongsTo(FinishedProduct::class, 'item_id');
    }

    /**
     * Related SemiFinishedProduct when item_type === 'semi-finished'
     */
    public function semiFinishedProduct()
    {
        return $this->belongsTo(SemiFinishedProduct::class, 'item_id');
    }
}
