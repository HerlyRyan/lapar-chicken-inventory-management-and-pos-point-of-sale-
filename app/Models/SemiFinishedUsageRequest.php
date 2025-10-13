<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SemiFinishedUsageRequest extends Model
{
    use HasFactory;

    /**
     * Use semi-finished naming for the underlying table.
     * This model represents a Semi-Finished Usage Request entity.
     */
    protected $table = 'semi_finished_usage_requests';

    protected $fillable = [
        'request_number',
        'requesting_branch_id',
        'user_id',
        'status',
        'requested_date',
        'required_date',
        'purpose',
        'notes',
        'approval_notes',
        'rejection_reason',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'requested_date' => 'date',
        'required_date' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the branch that made the request
     */
    public function requestingBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'requesting_branch_id')->withDefault();
    }

    /**
     * Get the user who created the request
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Backward-compatible alias used in some Blade views
     * Example usage: $materialUsageRequest->user->name
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    /**
     * Get the user who approved the request
     */
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Backward-compatible alias used in some Blade views
     * Example usage: $materialUsageRequest->approvalUser->name
     */
    public function approvalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get all items in the semi-finished usage request
     */
    public function items(): HasMany
    {
        // Explicitly specify the foreign key to match updated schema
        // Table: semi_finished_usage_request_items
        // FK: semi_finished_request_id -> semi_finished_usage_requests.id
        return $this->hasMany(SemiFinishedUsageRequestItem::class, 'semi_finished_request_id');
    }

    /**
     * Get planned target stocks (finished products) for this semi-finished usage request
     */
    public function targets(): HasMany
    {
        return $this->hasMany(SemiFinishedUsageRequestTarget::class, 'semi_finished_request_id');
    }

    /**
     * Get output records (planned vs actual) for this request
     */
    public function outputs(): HasMany
    {
        return $this->hasMany(SemiFinishedUsageRequestOutput::class, 'semi_finished_request_id');
    }

    /**
     * Get the total value of all items in the request
     */
    public function getTotalValueAttribute()
    {
        return $this->items()->sum(\DB::raw('quantity * unit_price'));
    }

    /**
     * Get formatted total value
     */
    public function getFormattedTotalValueAttribute()
    {
        return 'Rp ' . number_format($this->total_value, 0, ',', '.');
    }

    /**
     * Get status label for display
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Menunggu Persetujuan',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_PROCESSING => 'Sedang Diproses',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get status badge for display
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => '<span class="badge bg-warning">Menunggu Persetujuan</span>',
            self::STATUS_APPROVED => '<span class="badge bg-success">Disetujui</span>',
            self::STATUS_REJECTED => '<span class="badge bg-danger">Ditolak</span>',
            self::STATUS_PROCESSING => '<span class="badge bg-info">Sedang Diproses</span>',
            self::STATUS_COMPLETED => '<span class="badge bg-primary">Selesai</span>',
            self::STATUS_CANCELLED => '<span class="badge bg-secondary">Dibatalkan</span>',
            default => '<span class="badge bg-light">Tidak Diketahui</span>'
        };
    }

    /**
     * Backward-compatible accessor for approval_date used by some views
     * Maps to the existing approved_at datetime cast
     */
    public function getApprovalDateAttribute()
    {
        return $this->approved_at;
    }

    /**
     * Backward-compatible accessor for total_amount used by some views
     * Reuses existing total_value computation
     */
    public function getTotalAmountAttribute()
    {
        return $this->total_value;
    }

    /**
     * Generate a unique request number
     */
    public static function generateRequestNumber()
    {
        $prefix = 'SFR';
        $date = date('Ymd');
        $lastRequest = self::where('request_number', 'like', $prefix . $date . '%')
            ->orderBy('request_number', 'desc')
            ->first();

        $sequenceNumber = '0001';

        if ($lastRequest) {
            $lastSequence = substr($lastRequest->request_number, -4);
            $sequenceNumber = str_pad((int) $lastSequence + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . $date . $sequenceNumber;
    }
}
