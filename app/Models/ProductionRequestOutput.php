<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionRequestOutput extends Model
{
    use HasFactory;

    protected $table = 'production_request_outputs';

    protected $fillable = [
        'production_request_id',
        'semi_finished_product_id',
        'planned_quantity',
        'actual_quantity',
        'notes',
    ];

    protected $casts = [
        'planned_quantity' => 'decimal:3',
        'actual_quantity' => 'decimal:3',
    ];

    public function productionRequest(): BelongsTo
    {
        return $this->belongsTo(ProductionRequest::class);
    }

    public function semiFinishedProduct(): BelongsTo
    {
        return $this->belongsTo(SemiFinishedProduct::class);
    }
}
