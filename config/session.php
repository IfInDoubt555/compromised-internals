<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Session Driver
    |--------------------------------------------------------------------------
    |
    | Supported drivers: "file", "cookie", "database", "apc",
    | "memcached", "redis", "dynamodb", "array"
    |
    */

    'driver' => env('SESSION_DRIVER', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime
    |--------------------------------------------------------------------------
    |
    | Minutes the session may remain idle.  
    | Setting expire_on_close to true will drop sessions on browser close.
    |
    */

    'lifetime'        => (int) env('SESSION_LIFETIME', 15),
    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', true),

    /*
    |--------------------------------------------------------------------------
    | Session Encryption
    |--------------------------------------------------------------------------
    |
    | When enabled, session payloads are encrypted at rest.
    |
    */

    'encrypt' => env('SESSION_ENCRYPT', true),

    /*
    |--------------------------------------------------------------------------
    | Session File Location
    |--------------------------------------------------------------------------
    */

    'files' => storage_path('framework/sessions'),

    /*
    |--------------------------------------------------------------------------
    | Session Database Connection & Table
    |--------------------------------------------------------------------------
    */

    'connection' => env('SESSION_CONNECTION', null),
    'table'      => env('SESSION_TABLE', 'sessions'),

    /*
    |--------------------------------------------------------------------------
    | Cache Store (for drivers like redis, memcached)
    |--------------------------------------------------------------------------
    */

    'store' => env('SESSION_STORE', null),

    /*
    |--------------------------------------------------------------------------
    | Session Sweeping Lottery
    |--------------------------------------------------------------------------
    |
    | With [0, 100], pruning only happens via your scheduled task—not randomly.
    |
    */

    'lottery' => [0, 100],

    /*
    |--------------------------------------------------------------------------
    | Session Cookie Settings
    |--------------------------------------------------------------------------
    */

    'cookie'   => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_') . '_session'
    ),
    'path'     => env('SESSION_PATH', '/'),
    'domain'   => env('SESSION_DOMAIN', null),
    'secure'   => env('SESSION_SECURE_COOKIE', true),
    'http_only'=> env('SESSION_HTTP_ONLY', true),
    'same_site'=> env('SESSION_SAME_SITE', 'strict'),
    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),

];
