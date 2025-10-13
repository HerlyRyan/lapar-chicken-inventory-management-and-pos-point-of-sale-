<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_request_id',
        'raw_material_id',
        'requested_quantity',
        'unit_cost',
        'total_cost',
        'notes'
    ];

    protected $casts = [
        'requested_quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    // Relationships
    public function productionRequest(): BelongsTo
    {
        return $this->belongsTo(ProductionRequest::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    // Helper methods
    public function calculateTotalCost(): void
    {
        $this->total_cost = (float)$this->requested_quantity * (float)$this->unit_cost;
    }

    // Automatically calculate total cost when saving
    protected static function booted()
    {
        static::saving(function ($item) {
            $item->calculateTotalCost();
        });
    }
}
