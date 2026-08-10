<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Auth;

/**
 * A shop's or merchant's access/refresh token pair.
 *
 * Applications are responsible for persisting this between requests
 * (the SDK does not perform any storage itself).
 */
final class AccessToken
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $refreshToken,
        public readonly int $expiresAt,
        public readonly ?int $shopId = null,
        public readonly ?int $merchantId = null,
        public readonly ?int $principalId = null,
        public readonly ?int $userId = null,
    ) {
    }

    /**
     * @param array<string, mixed> $response Decoded GetAccessToken / RefreshAccessToken response.
     */
    public static function fromResponse(array $response, ?int $shopId = null, ?int $merchantId = null, ?int $principalId = null, ?int $userId = null): self
    {
        return new self(
            accessToken: (string) $response['access_token'],
            refreshToken: (string) $response['refresh_token'],
            expiresAt: time() + (int) $response['expire_in'],
            shopId: $shopId ?? (isset($response['shop_id']) ? (int) $response['shop_id'] : null),
            merchantId: $merchantId ?? (isset($response['merchant_id']) ? (int) $response['merchant_id'] : null),
            principalId: $principalId ?? (isset($response['principal_id']) ? (int) $response['principal_id'] : null),
            userId: $userId ?? (isset($response['user_id']) ? (int) $response['user_id'] : null),
        );
    }

    public function isExpired(int $bufferSeconds = 60): bool
    {
        return time() >= ($this->expiresAt - $bufferSeconds);
    }
}
