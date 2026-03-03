<?php

return [
    'merchant_id'   => env('PAYTR_MERCHANT_ID'),
    'merchant_key'  => env('PAYTR_MERCHANT_KEY'),
    'merchant_salt' => env('PAYTR_MERCHANT_SALT'),
    'success_url'   => env('PAYTR_SUCCESS_URL', '/checkout/success'),
    'fail_url'      => env('PAYTR_FAIL_URL', '/checkout/fail'),
    'callback_url'  => env('PAYTR_CALLBACK_URL', '/api/paytr/callback'),
    'test_mode'     => env('PAYTR_TEST_MODE', true),
    'debug'         => env('PAYTR_DEBUG', false),
    'timeout'       => 30,
    'allowed_ips'   => ['193.140.143.0/24'],
    'iframe_url'    => 'https://www.paytr.com/odeme/guvenli/',
    'api_url'       => 'https://www.paytr.com/odeme/api/get-token',
];
