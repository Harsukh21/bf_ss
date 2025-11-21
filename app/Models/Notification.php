<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'notification_type',
        'duration_value',
        'daily_time',
        'weekly_day',
        'weekly_time',
        'monthly_day',
        'monthly_time',
        'scheduled_at',
        'delivery_methods',
        'status',
        'created_by',
        'requires_web_pin',
    ];

    protected $casts = [
        'delivery_methods' => 'array',
        'scheduled_at' => 'datetime',
        'requires_web_pin' => 'boolean',
        'daily_time' => 'datetime:H:i',
        'weekly_time' => 'datetime:H:i',
        'monthly_time' => 'datetime:H:i',
    ];

    /**
     * Get the user who created this notification
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users who should receive this notification
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_user')
                    ->withPivot('is_read', 'read_at', 'is_delivered', 'delivered_at', 'delivery_status')
                    ->withTimestamps();
    }
}
