<?php

/**
 * Default SDK configuration.
 *
 * These are non-sensitive defaults only. Partner credentials, access tokens
 * and refresh tokens must never be stored here — they belong to the
 * application, not the SDK.
 */

return [
    /*
     * Shopee Open Platform API base URI, without a trailing slash.
     *
     * Production (choose the domain closest to your servers):
     *   - https://partner.shopeemobile.com
     *   - https://openplatform.shopee.cn
     *   - https://openplatform.shopee.com.br
     *
     * Sandbox:
     *   - https://openplatform.sandbox.test-stable.shopee.sg
     */
    'api_url' => 'https://partner.shopeemobile.com',

    /*
     * Authorization (OAuth) base URI, without a trailing slash.
     *
     * Production: https://open.shopee.com
     * Sandbox:    https://open.test-stable.shopee.com
     */
    'auth_url' => 'https://open.shopee.com',

    'hash' => [
        'algorithm' => 'sha256',
    ],

    'http' => [
        'timeout' => 30,
        'connect_timeout' => 10,
    ],
];
