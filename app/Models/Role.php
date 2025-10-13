<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
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

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class);
    }

    public function hasPermission($permissionCode)
    {
        return $this->permissions()->where('code', $permissionCode)->exists();
    }

    /**
     * Check if this role is a Super Admin role
     */
    public function isSuperAdmin()
    {
        return $this->code === 'super_admin' || $this->name === 'Super Admin';
    }

    /**
     * Check if this is the primary (first) Super Admin role
     */
    public function isPrimarySuperAdmin()
    {
        if (!$this->isSuperAdmin()) return false;
        $first = self::where(function($q){
            $q->where('code','super_admin')->orWhere('name','Super Admin');
        })->orderBy('id')->first();
        return $first && $first->id === $this->id;
    }

    /**
     * Can this role be deleted?
     */
    public function deletable()
    {
        return !$this->isPrimarySuperAdmin();
    }
}
