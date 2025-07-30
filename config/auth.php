<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application.
    |
    */

    // 'defaults' => [
    //     'guard' => env('AUTH_GUARD', 'web'),
    //     'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    // ],
 'defaults' => [
    'guard' => 'web',
    'passwords' => 'users',
],
    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Define every authentication guard for your application.
    | The "web" guard uses session driver.
    | The "api" guard uses Sanctum driver.
    |
    */

//    'guards' => [
//     'web' => [
//         'driver' => 'session',
//         'provider' => 'users',
        
//     ],
//     'api' => [
//         'driver' => 'sanctum',
//         'provider' => 'users',
//     ],
// ],

'guards' => [
    'sanctum' => [
        'driver' => 'sanctum',
        'provider' => 'users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',  // or 'database' if you're using the database provider
        'model' => App\Models\User::class,  // Make sure it's pointing to your User model
    ],
],




    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Define how users are retrieved from your database.
    |
    */


    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Configure password reset settings.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout for password confirmation.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
