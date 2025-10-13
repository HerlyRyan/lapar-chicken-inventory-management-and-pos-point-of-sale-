<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_code',
        'branch_id',
        'requested_by',
        'purpose',
        'total_raw_material_cost',
        'estimated_output_quantity',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'production_started_by',
        'production_started_at',
        'production_completed_by',
        'production_completed_at',
        'production_notes'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'production_started_at' => 'datetime',
        'production_completed_at' => 'datetime',
        'total_raw_material_cost' => 'decimal:2',
    ];

    /**
     * Auto-fill branch_id for Kepala Produksi on create
     */
    protected static function booted(): void
    {
        static::creating(function ($model) {
            $user = auth()->user();
            // If creator is Kepala Produksi, force branch_id to their branch
            if ($user && method_exists($user, 'hasRole') && $user->hasRole('Kepala Produksi')) {
                // Load user's branch
                $branch = $user->relationLoaded('branch') ? $user->branch : $user->branch()->first();

                if ($branch && ($branch->type ?? null) === 'production') {
                    $model->branch_id = $branch->id;
                    return;
                }

                // Guard/fallback: find any production branch (e.g., Pusat Produksi)
                $productionBranch = Branch::where('type', 'production')->orderBy('id')->first();
                if ($productionBranch) {
                    $model->branch_id = $productionBranch->id;
                    return;
                }

                // As a last resort, block creation with a clear message
                throw new \RuntimeException('Kepala Produksi harus terasosiasi ke Cabang bertipe production. Mohon hubungi admin untuk mengatur cabang Anda.');
            }
        });
    }

    // Relationships
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function productionStartedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'production_started_by');
    }

    public function productionCompletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'production_completed_by');
    }

    /**
     * The branch (production center) that owns this request
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class)->withDefault(function ($branch) {
            // Fallback to the default Production Center to avoid "Unknown" in dashboards
            $default = Branch::where('type', 'production')->orderBy('id')->first();
            if ($default) {
                $branch->id = $default->id;
                $branch->name = $default->name;
                $branch->code = $default->code;
            } else {
                // Sensible hard fallback when no production branch exists
                $branch->name = 'Pusat Produksi';
                $branch->code = 'CENTER';
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductionRequestItem::class);
    }

    /**
     * Get planned/actual semi-finished outputs for this request
     */
    public function outputs(): HasMany
    {
        return $this->hasMany(ProductionRequestOutput::class);
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'in_progress' => 'Sedang Diproduksi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'in_progress' => 'info',
            'completed' => 'primary',
            'cancelled' => 'secondary',
            default => 'secondary'
        };
    }

    // Generate unique request code
    public static function generateRequestCode(): string
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now())->count() + 1;
        return 'PR-' . $date . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
