<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_name',
        'abbreviation',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class);
    }

    public function finishedProducts()
    {
        return $this->hasMany(FinishedProduct::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Accessors
    public function getNameAttribute()
    {
        return $this->unit_name;
    }

    // Helper methods
    public function getDisplayNameAttribute()
    {
        return $this->unit_name . ' (' . $this->abbreviation . ')';
    }

    public function canBeDeleted()
    {
        return !$this->rawMaterials()->exists() && !$this->finishedProducts()->exists();
    }
}
