<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'date',
        'start_time',
        'end_time',
        'start_break_time',
        'end_break_time',
        'working_minutes',
        'status',
        'note',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Calculate working minutes from time strings.
     * working = (end - start) - break_duration
     */
    public static function calculateWorkingMinutes(
        ?string $startTime,
        ?string $endTime,
        ?string $startBreak,
        ?string $endBreak
    ): ?int {
        if (!$startTime || !$endTime) {
            return null;
        }

        $start = strtotime($startTime);
        $end   = strtotime($endTime);

        if ($end <= $start) {
            return null;
        }

        $total = ($end - $start) / 60;

        if ($startBreak && $endBreak) {
            $bs = strtotime($startBreak);
            $be = strtotime($endBreak);
            if ($be > $bs) {
                $total -= ($be - $bs) / 60;
            }
        }

        return (int) max(0, $total);
    }

    /**
     * Format working_minutes as H:i:s
     */
    public function getWorkingTimeAttribute(): string
    {
        if ($this->working_minutes === null) {
            return '—';
        }
        $h = intdiv($this->working_minutes, 60);
        $m = $this->working_minutes % 60;
        return sprintf('%02d:%02d:00', $h, $m);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'present'  => 'green',
            'absent'   => 'red',
            'half_day' => 'yellow',
            'late'     => 'orange',
            'on_leave' => 'blue',
            'holiday'  => 'purple',
            default    => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present'  => 'Present',
            'absent'   => 'Absent',
            'half_day' => 'Half Day',
            'late'     => 'Late',
            'on_leave' => 'On Leave',
            'holiday'  => 'Holiday',
            default    => ucfirst($this->status),
        };
    }
}
