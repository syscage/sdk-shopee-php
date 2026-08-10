<?php

/**
 * Published Laravel configuration for syscage/sdk-shopee-php.
 *
 * Unlike src/Core/Config/sdk-shopee.php (non-sensitive SDK defaults only),
 * this file is app-owned once published and may read partner credentials
 * from the environment, following normal Laravel convention.
 */

return [
    'partner_id' => env('SHOPEE_PARTNER_ID'),
    'partner_key' => env('SHOPEE_PARTNER_KEY'),

    'api_url' => env('SHOPEE_API_URL', 'https://partner.shopeemobile.com'),
    'auth_url' => env('SHOPEE_AUTH_URL', 'https://open.shopee.com'),

    'hash' => [
        'algorithm' => env('SHOPEE_HASH_ALGORITHM', 'sha256'),
    ],

    'http' => [
        'timeout' => (int) env('SHOPEE_HTTP_TIMEOUT', 30),
        'connect_timeout' => (int) env('SHOPEE_HTTP_CONNECT_TIMEOUT', 10),
    ],
];
