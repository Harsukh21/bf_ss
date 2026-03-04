<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'date',
        'description',
        'is_recurring',
        'created_by',
    ];

    protected $casts = [
        'date'         => 'date',
        'is_recurring' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function isHoliday(\Carbon\Carbon $date): bool
    {
        return self::query()
            ->where(function ($q) use ($date) {
                // Exact date match
                $q->where('date', $date->toDateString())
                  ->where('is_recurring', false);
            })
            ->orWhere(function ($q) use ($date) {
                // Recurring: same month/day any year
                $q->whereMonth('date', $date->month)
                  ->whereDay('date', $date->day)
                  ->where('is_recurring', true);
            })
            ->exists();
    }

    public static function getForYear(int $year)
    {
        return self::query()
            ->where(function ($q) use ($year) {
                $q->whereYear('date', $year)
                  ->where('is_recurring', false);
            })
            ->orWhere('is_recurring', true)
            ->orderBy('date')
            ->get();
    }
}
