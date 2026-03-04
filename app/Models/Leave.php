<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    protected $fillable = [
        'user_id',
        'leave_type',
        'from_date',
        'to_date',
        'total_days',
        'reason',
        'status',
        'approved_by',
        'approved_at',
        'admin_notes',
    ];

    protected $casts = [
        'from_date'   => 'date',
        'to_date'     => 'date',
        'approved_at' => 'datetime',
        'total_days'  => 'float',
    ];

    public static array $leaveTypes = [
        'L'  => 'Casual Leave',
        'SL' => 'Sick Leave',
        'H'  => 'Holiday',
        'CO' => 'Compensatory Off',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getLeaveTypeLabelAttribute(): string
    {
        return self::$leaveTypes[$this->leave_type] ?? $this->leave_type;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'   => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>',
            'approved'  => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Approved</span>',
            'rejected'  => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">Rejected</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Cancelled</span>',
            default     => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">Unknown</span>',
        };
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
