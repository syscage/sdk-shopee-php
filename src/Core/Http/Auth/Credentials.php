<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Auth;

/**
 * Partner-level Shopee Open Platform credentials and environment endpoints.
 *
 * Never log or serialize this object — {@see $partnerKey} is a secret.
 */
final class Credentials
{
    public readonly string $apiUrl;
    public readonly string $authUrl;

    public function __construct(
        public readonly int $partnerId,
        public readonly string $partnerKey,
        string $apiUrl,
        string $authUrl,
    ) {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->authUrl = rtrim($authUrl, '/');
    }
}
