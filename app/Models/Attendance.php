<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'login_time',
        'break_start_time',
        'break_end_time',
        'logout_time',
        'total_hours',
        'break_duration',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate and store total hours and break duration.
     */
    public function recalculateHours(): void
    {
        $breakDuration = 0;

        if ($this->break_start_time && $this->break_end_time) {
            $breakStart = Carbon::createFromTimeString($this->break_start_time);
            $breakEnd   = Carbon::createFromTimeString($this->break_end_time);
            $breakDuration = round($breakEnd->diffInMinutes($breakStart) / 60, 2);
        }

        $totalHours = null;
        if ($this->login_time && $this->logout_time) {
            $login  = Carbon::createFromTimeString($this->login_time);
            $logout = Carbon::createFromTimeString($this->logout_time);
            $gross  = $logout->diffInMinutes($login) / 60;
            $totalHours = round(max(0, $gross - $breakDuration), 2);
        }

        $this->break_duration = $breakDuration ?: null;
        $this->total_hours    = $totalHours;

        // Auto-determine status
        if ($this->status !== 'on_leave' && $this->status !== 'holiday') {
            if ($this->login_time && $this->logout_time) {
                $this->status = ($totalHours < 4) ? 'half_day' : 'present';
            } elseif ($this->login_time) {
                $this->status = 'incomplete';
            } else {
                $this->status = 'absent';
            }
        }

        $this->save();
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('date', $date);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    public function getFormattedTotalHoursAttribute(): string
    {
        if (!$this->total_hours) {
            return '--';
        }
        $hours   = floor($this->total_hours);
        $minutes = round(($this->total_hours - $hours) * 60);
        return "{$hours}h {$minutes}m";
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'present'   => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Present</span>',
            'absent'    => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">Absent</span>',
            'half_day'  => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">Half Day</span>',
            'on_leave'  => '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">On Leave</span>',
            'holiday'   => '<span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">Holiday</span>',
            'incomplete'=> '<span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300">Incomplete</span>',
            default     => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">Unknown</span>',
        };
    }
}
