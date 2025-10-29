<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sports Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration contains the mapping of sport IDs to sport names.
    | Used throughout the application to display sport names instead of IDs.
    |
    */

    'sports' => [
        1 => 'Soccer',
        2 => 'Tennis',
        4 => 'Cricket',
        6 => 'Boxing',
        7 => 'HR',
        9 => 'Pro Kabaddi',
        7522 => 'Basketball',
        76548427 => 'Politics',
    ],

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    |
    | These helper methods can be used throughout the application to work
    | with sport data.
    |
    */

    'get_sport_name' => function ($sportId) {
        return config('sports.sports')[$sportId] ?? 'Unknown Sport';
    },

    'get_all_sports' => function () {
        return config('sports.sports');
    },

    'get_sport_options' => function () {
        return array_map(function ($id, $name) {
            return ['id' => $id, 'name' => $name];
        }, array_keys(config('sports.sports')), config('sports.sports'));
    },
];
