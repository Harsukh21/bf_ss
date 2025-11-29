<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Labels Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines the available labels that can be assigned
    | to markets or events. Labels are stored as JSON in the database.
    |
    | To add a new label, simply add a new entry to the 'labels' array below.
    | The key is the label identifier (lowercase) and the value is the
    | display name (will be shown in uppercase by default).
    |
    */

    'labels' => [
        '4x' => '4X',
        'b2c' => 'B2C',
        'b2b' => 'B2B',
        'usdt' => 'USDT',
        'bookmaker' => 'BOOKMAKER',
        'unmatch' => 'UNMATCH',
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    |
    | These helper methods can be used throughout the application to work
    | with label data.
    |
    */

    'get_all_labels' => function () {
        return config('labels.labels', []);
    },

    'get_label_keys' => function () {
        return array_keys(config('labels.labels', []));
    },

    'get_label_name' => function ($key) {
        return config("labels.labels.{$key}", strtoupper($key));
    },
];

