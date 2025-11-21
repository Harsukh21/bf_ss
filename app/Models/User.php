<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'web_pin',
        'telegram_id',
        'telegram_chat_id',
        'first_name',
        'last_name',
        'phone',
        'bio',
        'avatar',
        'date_of_birth',
        'timezone',
        'language',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'last_login_at',
        'last_login_ip',
        'last_login_user_agent',
        'login_history',
        'password_changed_at',
        'current_session_id',
        'active_sessions',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'login_history' => 'array',
            'password_changed_at' => 'datetime',
            'active_sessions' => 'array',
            'telegram_chat_id' => 'integer',
        ];
    }

    /**
     * Get the roles that belong to the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Get all permissions for the user through their roles.
     */
    public function permissions()
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->unique('id');
    }

    /**
     * Cache key prefix for user permissions
     */
    public function getPermissionsCacheKey(): string
    {
        return "user_permissions_{$this->id}";
    }

    /**
     * Cache key prefix for user roles
     */
    public function getRolesCacheKey(): string
    {
        return "user_roles_{$this->id}";
    }

    /**
     * Load and cache all user permissions (via roles)
     */
    public function loadPermissionsIntoCache(): array
    {
        $permissions = $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')
            ->flatten()
            ->unique('id')
            ->pluck('slug')
            ->toArray();

        // Cache for 24 hours (1440 minutes)
        Cache::put($this->getPermissionsCacheKey(), $permissions, now()->addMinutes(1440));

        return $permissions;
    }

    /**
     * Load and cache all user roles
     */
    public function loadRolesIntoCache(): array
    {
        $roles = $this->roles()->pluck('slug')->toArray();

        // Cache for 24 hours
        Cache::put($this->getRolesCacheKey(), $roles, now()->addMinutes(1440));

        return $roles;
    }

    /**
     * Clear user permission cache
     */
    public function clearPermissionCache(): void
    {
        Cache::forget($this->getPermissionsCacheKey());
        Cache::forget($this->getRolesCacheKey());
    }

    /**
     * Get cached permissions (load if not cached)
     */
    public function getCachedPermissions(): array
    {
        return Cache::remember($this->getPermissionsCacheKey(), now()->addMinutes(1440), function () {
            return $this->loadPermissionsIntoCache();
        });
    }

    /**
     * Get cached roles (load if not cached)
     */
    public function getCachedRoles(): array
    {
        return Cache::remember($this->getRolesCacheKey(), now()->addMinutes(1440), function () {
            return $this->loadRolesIntoCache();
        });
    }

    /**
     * Check if user has a specific role (using cache).
     */
    public function hasRole($role): bool
    {
        $roles = $this->getCachedRoles();
        
        if (is_string($role)) {
            return in_array($role, $roles);
        }
        
        // If role is a model instance, check by slug
        if (is_object($role) && method_exists($role, 'getAttribute')) {
            return in_array($role->slug, $roles);
        }
        
        return false;
    }

    /**
     * Check if user has a specific permission (using cache).
     */
    public function hasPermission($permission): bool
    {
        // Super Admin has all permissions
        if ($this->hasRole('super-admin')) {
            return true;
        }

        $permissions = $this->getCachedPermissions();
        
        if (is_string($permission)) {
            return in_array($permission, $permissions);
        }
        
        // If permission is a model instance, check by slug
        if (is_object($permission) && method_exists($permission, 'getAttribute')) {
            return in_array($permission->slug, $permissions);
        }
        
        return false;
    }

    /**
     * Assign roles to the user.
     */
    public function assignRoles(array $roleIds)
    {
        return $this->roles()->sync($roleIds);
    }

    /**
     * Remove all roles from the user.
     */
    public function removeRoles()
    {
        return $this->roles()->detach();
    }

    /**
     * Get the notifications for this user
     */
    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_user')
                    ->withPivot('is_read', 'read_at', 'is_delivered', 'delivered_at', 'delivery_status')
                    ->withTimestamps()
                    ->orderBy('created_at', 'desc');
    }
}
