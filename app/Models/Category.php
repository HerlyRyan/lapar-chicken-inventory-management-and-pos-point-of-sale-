<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // No material type constants needed anymore

    // Relationships
    public function finishedProducts()
    {
        return $this->hasMany(FinishedProduct::class);
    }

    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class);
    }

    public function semiFinishedProducts()
    {
        return $this->hasMany(SemiFinishedProduct::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Removed scopeForMaterialType method as material_type field is no longer used

    // Removed scopeForRawMaterials method as material_type field is no longer used

    // Removed scopeForSemiFinished method as material_type field is no longer used

    // Scope to get categories for finished products
    public function scopeForFinishedProducts($query)
    {
        return $query->active();
    }

    // Helper methods
    public function getProductsCountAttribute()
    {
        $count = 0;
        $count += $this->finishedProducts()->count();
        $count += $this->rawMaterials()->count();
        $count += $this->semiFinishedProducts()->count();
        return $count;
    }

    public function getActiveProductsCountAttribute()
    {
        $count = 0;
        $count += $this->finishedProducts()->where('is_active', true)->count();
        $count += $this->rawMaterials()->where('is_active', true)->count();
        $count += $this->semiFinishedProducts()->where('is_active', true)->count();
        return $count;
    }

    // Removed getMaterialTypeDisplayAttribute method as material_type field is no longer used

    // Removed getMaterialTypeIconAttribute method as material_type field is no longer used

    // Removed getMaterialTypes method as material_type field is no longer used
}
