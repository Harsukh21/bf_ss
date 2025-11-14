<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        '_id',
        'eventId',
        'exEventId',
        'sportId',
        'tournamentsId',
        'tournamentsName',
        'eventName',
        'highlight',
        'quicklink',
        'popular',
        'IsSettle',
        'IsVoid',
        'IsUnsettle',
        'dataSwitch',
        'marketTime',
        'createdAt',
    ];

    protected $casts = [
        'highlight' => 'boolean',
        'quicklink' => 'boolean',
        'popular' => 'boolean',
        'IsSettle' => 'integer',
        'IsVoid' => 'integer',
        'IsUnsettle' => 'integer',
        'dataSwitch' => 'integer',
        'createdAt' => 'datetime',
    ];
}
