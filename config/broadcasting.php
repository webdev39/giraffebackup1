<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "pusher", "redis", "log", "null"
    |
    */

    'default' => env('BROADCAST_DRIVER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over websockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [

        // for socket io
        'pusher' => [
            'driver' => 'pusher',
            'key'    => env('PUSHER_APP_KEY'),
            'secret' => null,
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'auth'      => env('PUSHER_AUTH_URL'),
                'port'      => env('LARAVEL_ECHO_PORT'),
                'host'      => env('APP_URL'),
                'scheme'    => 'http'
            ],
        ],

        // for pusher
//        'pusher' => [
//            'driver' => 'pusher',
//            'key'    => env('PUSHER_APP_KEY'),
//            'secret' => env('PUSHER_APP_SECRET'),
//            'app_id' => env('PUSHER_APP_ID'),
//            'options' => [
//                'auth'      => env('PUSHER_AUTH_URL'),
//                'cluster'   => env('PUSHER_CLUSTER'),
//                'encrypted' => true,
//            ],
//        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
