<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Branch;
use App\Models\SemiFinishedProduct;

class SemiFinishedDistribution extends Model
{
    use HasFactory;

    protected $table = 'semi_finished_distributions';

    protected $fillable = [
        'distribution_code',
        'sent_by',
        'target_branch_id',
        'semi_finished_product_id',
        'quantity_sent',
        'unit_cost',
        'total_cost',
        'distribution_notes',
        'status',
        'handled_by',
        'handled_at',
        'response_notes',
    ];

    protected $casts = [
        'quantity_sent' => 'float',
        'unit_cost' => 'float',
        'total_cost' => 'float',
        'handled_at' => 'datetime',
    ];

    // Relationships
    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function targetBranch()
    {
        return $this->belongsTo(Branch::class, 'target_branch_id');
    }

    public function semiFinishedProduct()
    {
        return $this->belongsTo(SemiFinishedProduct::class, 'semi_finished_product_id');
    }

    // Alias used by Blade: $distribution->branch
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'target_branch_id');
    }

    // --------- Helpers for status checks used in Blade ---------
    public function isPending(): bool
    {
        // Map legacy/semantic statuses: 'sent' behaves as 'pending' in the UI
        return in_array($this->status, ['pending', 'sent'], true);
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // --------- Computed attributes for UI ---------
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'accepted' => 'Diterima',
            'rejected' => 'Ditolak',
            'sent', 'pending' => 'Dikirim',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'accepted' => 'success',
            'rejected' => 'danger',
            'sent', 'pending' => 'warning',
            default => 'secondary',
        };
    }

    // Used by Blade as $distribution->distribution_date
    public function getDistributionDateAttribute()
    {
        // No dedicated column; use created_at as the distribution date
        return $this->created_at;
    }

    // Accessors to align accepted_* and rejected_* usage in Blade with handled_* columns
    public function getAcceptedByAttribute()
    {
        return $this->isAccepted() ? $this->handledBy : null;
    }

    public function getAcceptedAtAttribute()
    {
        return $this->isAccepted() ? $this->handled_at : null;
    }

    public function getRejectedByAttribute()
    {
        return $this->isRejected() ? $this->handledBy : null;
    }

    public function getRejectedAtAttribute()
    {
        return $this->isRejected() ? $this->handled_at : null;
    }

    // Bridge accessors to align with views expecting Distribution-like API
    public function getFromBranchAttribute()
    {
        // Prefer the branch of the sender (user) if available
        if ($this->sentBy && $this->sentBy->branch) {
            return $this->sentBy->branch;
        }

        // Fallback: use the Production Center branch so UI doesn't show "Unknown"
        // Try scope production() if available; otherwise filter by type column
        $default = method_exists(Branch::class, 'production')
            ? Branch::production()->orderBy('id')->first()
            : Branch::where('type', 'production')->orderBy('id')->first();

        return $default; // may be null if not configured; Blade will handle gracefully
    }

    public function getToBranchAttribute()
    {
        // Target branch of the distribution
        return $this->targetBranch;
    }

    public function getStockableAttribute()
    {
        // Align with views referencing $distribution->stockable->name
        return $this->semiFinishedProduct;
    }

    public function getQuantityAttribute()
    {
        // Align with views referencing $distribution->quantity
        return $this->quantity_sent;
    }

    // Lightweight items collection so Blade code that expects items works.
    // Each distribution row represents a single product in this implementation.
    public function getItemsAttribute()
    {
        // Return a collection with one pseudo-item exposing semiFinishedProduct
        return collect([(object) [
            'semiFinishedProduct' => $this->semiFinishedProduct,
        ]]);
    }
}
