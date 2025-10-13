<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'password',
        'branch_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relations
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Check if user has Super Admin role
     */
    public function isSuperAdmin()
    {
        return $this->roles()->where(function($q){
            $q->where('code','super_admin')->orWhere('name','Super Admin');
        })->exists();
    }

    /**
     * Check if this is the first (primary) Super Admin user
     */
    public function isPrimarySuperAdmin()
    {
        if (!$this->isSuperAdmin()) return false;
        $first = self::whereHas('roles', function($q){
            $q->where('code','super_admin')->orWhere('name','Super Admin');
        })->orderBy('id')->first();
        return $first && $first->id === $this->id;
    }

    /**
     * Can this user be deleted?
     */
    public function deletable()
    {
        return !$this->isPrimarySuperAdmin();
    }

    public function userRoles()
    {
        return $this->hasMany(UserRole::class);
    }

    // Permission methods
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermission($permissionCode)
    {
        return $this->roles()->whereHas('permissions', function($query) use ($permissionCode) {
            $query->where('code', $permissionCode);
        })->exists();
    }

    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }
        
        return $this->roles()->whereIn('code', $roles)->exists();
    }

    public function hasAnyPermission($permissions)
    {
        if (is_string($permissions)) {
            return $this->hasPermission($permissions);
        }
        
        return $this->roles()->whereHas('permissions', function($query) use ($permissions) {
            $query->whereIn('code', $permissions);
        })->exists();
    }

    public function getAllPermissions()
    {
        return Permission::whereHas('rolePermissions.role.userRoles', function($query) {
            $query->where('user_id', $this->id);
        })->get();
    }

    public function getHighestRole()
    {
        return $this->roles()->orderBy('name')->first();
    }

    // Helper method for avatar initials
    public function getAvatarInitials()
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    // Helper method for avatar URL or default
    public function getAvatarUrl()
    {
        return $this->avatar ? asset($this->avatar) : null;
    }
}
