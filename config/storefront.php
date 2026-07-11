<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Storefront Ordering
    |--------------------------------------------------------------------------
    |
    | Keep the catalogue visible while temporarily preventing cart and checkout
    | mutations. Set STOREFRONT_ORDERS_ENABLED=true when real product data and
    | payment operations are ready.
    |
    */
    'orders_enabled' => env('STOREFRONT_ORDERS_ENABLED', env('APP_ENV') === 'testing'),
];
