<?php

return [
    'api_url'        => env('SMS_API_URL'),
    'username'       => env('SMS_USERNAME'),
    'password'       => env('SMS_PASSWORD'),
    'subscriber_no'  => env('SMS_SUBSCRIBER_NO'),
    'sender_title'   => env('SMS_SENDER_TITLE', 'ROSEGARDEN'),
    'enabled'        => env('SMS_ENABLED', false),
];
