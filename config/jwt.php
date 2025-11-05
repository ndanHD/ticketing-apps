<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret
    |--------------------------------------------------------------------------
    |
    | Used to sign the tokens. Keep this very secure.
    |
    */
    'secret' => env('JWT_SECRET', 'your-secret-key'),

    /*
    |--------------------------------------------------------------------------
    | JWT TTL (Time To Live)
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token will be valid for.
    |
    */
    'ttl' => env('JWT_TTL', 60 * 24), // 24 hours

    /*
    |--------------------------------------------------------------------------
    | JWT Refresh TTL
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the refresh token will be valid for.
    |
    */
    'refresh_ttl' => env('JWT_REFRESH_TTL', 60 * 24 * 7), // 1 week
];