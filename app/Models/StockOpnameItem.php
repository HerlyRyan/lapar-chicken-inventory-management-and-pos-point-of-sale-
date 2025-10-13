<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id',
        'item_type',
        'item_id',
        'item_code',
        'item_name',
        'unit_abbr',
        'system_quantity',
        'real_quantity',
        'difference',
        'status',
        'unit_cost',
        'value_difference',
    ];

    protected $casts = [
        'system_quantity' => 'integer',
        'real_quantity' => 'integer',
        'difference' => 'integer',
        'unit_cost' => 'decimal:2',
        'value_difference' => 'decimal:2',
    ];

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function recompute(): void
    {
        $diff = (int)$this->real_quantity - (int)$this->system_quantity;
        $this->difference = $diff;
        $this->status = $diff === 0 ? 'matched' : ($diff > 0 ? 'over' : 'under');
        $this->value_difference = sprintf('%.2f', ((float) $this->unit_cost) * $diff);
    }
}
