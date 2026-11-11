<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    | SMS (Ilnia). Driver: "log" in development (nothing is sent, the text goes to
    | storage/logs), "kavenegar" in production. The API key lives only in .env.
    */
    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
    ],

    'kavenegar' => [
        'api_key' => env('KAVENEGAR_API_KEY'),
        'otp_template' => env('KAVENEGAR_OTP_TEMPLATE', 'otp'),
        'order_template' => env('KAVENEGAR_ORDER_TEMPLATE', 'order'),
        'base_url' => 'https://api.kavenegar.com/v1',
        'timeout' => 10,
    ],

];
