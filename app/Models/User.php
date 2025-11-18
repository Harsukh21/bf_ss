<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
     * Check if user has a specific role.
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->where('slug', $role)->exists();
        }
        return $this->roles->contains($role);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            foreach ($this->roles as $role) {
                if ($role->permissions()->where('slug', $permission)->exists()) {
                    return true;
                }
            }
            return false;
        }
        foreach ($this->roles as $role) {
            if ($role->permissions->contains($permission)) {
                return true;
            }
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
}
