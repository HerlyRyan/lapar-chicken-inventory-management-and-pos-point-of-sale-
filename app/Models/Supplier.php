<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'address', 'phone', 'email', 'contact_person', 'is_active',
    ];

    /**
     * Get all raw materials supplied by this supplier.
     */
    public function materials()
    {
        return $this->hasMany(\App\Models\RawMaterial::class, 'supplier_id');
    }

    /**
     * Get all raw materials supplied by this supplier (alias for materials).
     */
    public function rawMaterials()
    {
        return $this->hasMany(\App\Models\RawMaterial::class, 'supplier_id');
    }
}
