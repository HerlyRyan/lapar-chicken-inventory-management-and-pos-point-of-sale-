<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory;

    protected $fillable = [
        'opname_number',
        'status',
        'product_type',
        'branch_id',
        'user_id',
        'submitted_at',
        'notes',
        'total_items',
        'matched_count',
        'over_count',
        'under_count',
        'match_percentage',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'match_percentage' => 'decimal:2',
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
        return $this->hasMany(StockOpnameItem::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function recalcSummary(): void
    {
        $total = $this->items()->count();
        $matched = $this->items()->where('status', 'matched')->count();
        $over = $this->items()->where('status', 'over')->count();
        $under = $this->items()->where('status', 'under')->count();

        $this->total_items = $total;
        $this->matched_count = $matched;
        $this->over_count = $over;
        $this->under_count = $under;
        // assign as string to satisfy decimal cast
        $this->match_percentage = $total > 0
            ? sprintf('%.2f', (($matched / max(1, $total)) * 100))
            : '0.00';
        $this->save();
    }
}
