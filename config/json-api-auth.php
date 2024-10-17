<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Method
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default authentication method that your API
    | will use. For example, you can choose between token-based authentication
    | or session-based authentication.
    |
    */

    'auth_method' => 'token',

    /*
    |--------------------------------------------------------------------------
    | Token Expiry Time
    |--------------------------------------------------------------------------
    |
    | This value determines the number of minutes before an issued token will
    | expire. You can adjust this value according to your application's needs.
    |
    */

    'token_expiry' => 60,

    /*
    |--------------------------------------------------------------------------
    | Refresh Token Expiry Time
    |--------------------------------------------------------------------------
    |
    | This value defines how long a refresh token is valid before it expires.
    | The refresh token allows users to get a new token without logging in again.
    |
    */

    'refresh_token_expiry' => 120,

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | Here you can specify the user model that should be used by the API for
    | authentication purposes. By default, Laravel's "User" model is used.
    |
    */

    'user_model' => App\Models\User::class,

    /*
    |--------------------------------------------------------------------------
    | Guards Configuration
    |--------------------------------------------------------------------------
    |
    | This section allows you to specify the guards that will be used for
    | authenticating API requests. You can define different guards for different
    | authentication methods, like 'jwt' or 'sanctum'.
    |
    */

    'guards' => [
        'api' => [
            'driver' => 'jwt',  // or 'sanctum' depending on your setup
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset Settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the password reset options, including the token
    | expiry time, as well as the name of the table that holds the tokens.
    |
    */

    'password_reset' => [
        'table' => 'password_resets',
        'expire' => 60,
    ],

    /*
    |--------------------------------------------------------------------------
    | Additional API Settings
    |--------------------------------------------------------------------------
    |
    | Any additional settings specific to your API can be added here. You can
    | customize the configurations as needed for your application's requirements.
    |
    */

    'additional_settings' => [
        // Add any other specific settings for your API here
    ],

    'email_account_was_already_verified_url' => 'https://your-frontend.com/already-verified',
    'email_account_just_verified_url' => 'https://your-frontend.com/verified',




];
