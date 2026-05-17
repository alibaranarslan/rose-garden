<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
        'maps_key' => env('GOOGLE_MAPS_KEY', ''),
    ],

    'google_translate' => [
        'api_key' => env('GOOGLE_TRANSLATE_API_KEY'),
    ],

    'google_analytics' => [
        'measurement_id' => env('GOOGLE_ANALYTICS_ID'),
    ],

    'paytr' => [
        'merchant_id' => env('PAYTR_MERCHANT_ID'),
        'merchant_key' => env('PAYTR_MERCHANT_KEY'),
        'merchant_salt' => env('PAYTR_MERCHANT_SALT'),
        'test_mode' => env('PAYTR_TEST_MODE', true),
        'debug' => env('PAYTR_DEBUG', false),
        'timeout' => env('PAYTR_TIMEOUT', 30),
        'api_url' => env('PAYTR_API_URL', 'https://www.paytr.com/odeme/api/get-token'),
        'iframe_url' => env('PAYTR_IFRAME_URL', 'https://www.paytr.com/odeme/guvenli/'),
        'success_url' => env('PAYTR_SUCCESS_URL', '/odeme/basarili'),
        'fail_url' => env('PAYTR_FAIL_URL', '/odeme/basarisiz'),
        'callback_url' => env('PAYTR_CALLBACK_URL', '/api/paytr/callback'),
        'allowed_ips' => array_filter(explode(',', (string) env('PAYTR_ALLOWED_IPS', '193.140.143.0/24'))),
    ],

    'sms' => [
        'api_url' => env('SMS_API_URL'),
        'username' => env('SMS_USERNAME'),
        'password' => env('SMS_PASSWORD'),
        'subscriber_no' => env('SMS_SUBSCRIBER_NO'),
        'sender_title' => env('SMS_SENDER_TITLE', 'ROSEGARDEN'),
        'enabled' => env('SMS_ENABLED', false),
    ],

];
