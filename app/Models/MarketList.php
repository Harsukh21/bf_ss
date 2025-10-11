<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketList extends Model
{
    protected $table = 'market_lists';

    protected $fillable = [
        '_id',
        'eventName',
        'exEventId',
        'exMarketId',
        'isPreBet',
        'marketName',
        'marketTime',
        'sportName',
        'tournamentsName',
        'type',
        'isLive',
    ];

    protected $casts = [
        'isPreBet' => 'boolean',
        'isLive' => 'boolean',
        'marketTime' => 'datetime',
    ];
}
