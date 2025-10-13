<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestructionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_number',
        'branch_id',
        'destruction_date',
        'reason',
        'total_cost',
        'status',
        'notes',
        'reported_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'destruction_date' => 'date',
        'total_cost' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function destructionReportItems()
    {
        return $this->hasMany(DestructionReportItem::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'draft' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
        ];

        return $statuses[$this->status] ?? 'bg-secondary';
    }

    public function getTotalCostAttribute($value)
    {
        // Fallback to sum of items when the column is null or not set
        if ($value === null) {
            return $this->destructionReportItems()->sum('total_cost');
        }
        return $value;
    }
}
