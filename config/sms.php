<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Domain
    |--------------------------------------------------------------------------
    |
    | This value is the domain of sms sender.
    |
    */

    'domain' => env('SMS_DOMAIN', 'https://smsplus.sslwireless.com'),

    /*
    |--------------------------------------------------------------------------
    | SMS SID
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'sid' => env('SMS_SID', ''),

    /*
    |--------------------------------------------------------------------------
    | SMS API Token
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'api_token' => env('SMS_API_TOKEN', ''),

];
