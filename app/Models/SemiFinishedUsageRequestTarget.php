<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class SemiFinishedUsageRequestTarget extends Model
{
    use HasFactory;

    // Keep compatibility with both old (targets) and new (outputs) table names
    protected $table = 'semi_finished_usage_request_targets';

    public function getTable()
    {
        // Prefer new table if present
        return Schema::hasTable('semi_finished_usage_request_outputs')
            ? 'semi_finished_usage_request_outputs'
            : 'semi_finished_usage_request_targets';
    }

    protected $fillable = [
        'semi_finished_request_id',
        // Prefer new schema column; keep legacy for compatibility
        'product_id',
        'finished_product_id',
        'planned_quantity',
        'actual_quantity',
        'notes',
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:3',
        'actual_quantity' => 'decimal:3',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(SemiFinishedUsageRequest::class, 'semi_finished_request_id');
    }

    public function finishedProduct(): BelongsTo
    {
        // Choose FK dynamically based on available column; outputs table uses product_id
        $fk = Schema::hasColumn($this->getTable(), 'product_id') ? 'product_id' : 'finished_product_id';
        return $this->belongsTo(FinishedProduct::class, $fk);
    }
}
