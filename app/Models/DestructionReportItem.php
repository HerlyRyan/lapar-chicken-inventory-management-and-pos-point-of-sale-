<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestructionReportItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'destruction_report_id',
        'item_type',
        'item_id',
        'quantity',
        'unit_cost',
        'total_cost',
        'condition_description',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2'
    ];

    public function destructionReport()
    {
        return $this->belongsTo(DestructionReport::class);
    }

    public function material()
    {
        // Note: do not constrain by item_type here to avoid cross-table where on related query
        return $this->belongsTo(Material::class, 'item_id');
    }

    public function finishedProduct()
    {
        // Note: do not constrain by item_type here to avoid cross-table where on related query
        return $this->belongsTo(FinishedProduct::class, 'item_id');
    }

    public function semiFinishedProduct()
    {
        // Note: do not constrain by item_type here to avoid cross-table where on related query
        return $this->belongsTo(SemiFinishedProduct::class, 'item_id');
    }

    public function getItemAttribute()
    {
        if ($this->item_type === 'material') {
            return Material::find($this->item_id);
        } elseif ($this->item_type === 'finished_product') {
            return FinishedProduct::find($this->item_id);
        } elseif ($this->item_type === 'semi_finished_product') {
            return SemiFinishedProduct::find($this->item_id);
        }

        return null;
    }
}
