<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MarketRate extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'exMarketId',
        'marketName',
        'runners',
        'inplay',
        'isCompleted',
    ];

    protected $casts = [
        'runners' => 'json',
        'inplay' => 'boolean',
        'isCompleted' => 'boolean',
    ];

    // Scope for inplay market rates
    public function scopeInplay($query)
    {
        return $query->where('inplay', true);
    }

    // Scope for completed market rates
    public function scopeCompleted($query)
    {
        return $query->where('isCompleted', true);
    }

    // Scope for specific market
    public function scopeByMarket($query, $marketName)
    {
        return $query->where('marketName', $marketName);
    }

    // Accessor for formatted market name
    public function getFormattedMarketNameAttribute()
    {
        return $this->marketName ?? 'Unknown Market';
    }
}
