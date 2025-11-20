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
        'web_pin',
        'telegram_id',
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
