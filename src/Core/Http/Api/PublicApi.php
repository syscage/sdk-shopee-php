<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Public API domain (`v2.public.*`) — partner-wide utilities: Shopee's
 * outbound IP ranges (for allowlisting), the list of shops/merchants
 * authorized to this partner, and an alternate resend-code token recovery
 * flow. Named `PublicApi` rather than `Public` because `public` is a
 * reserved PHP keyword and cannot be used as a class name.
 *
 * **Not duplicated here**: this Shopee doc category also documents
 * `get_access_token` (`/api/v2/auth/token/get`) and `refresh_access_token`
 * (`/api/v2/auth/access_token/get`) — those are the exact same endpoints
 * already implemented as {@see \Syscage\Sdk\Shopee\Core\Http\Auth\OAuth::getAccessTokenForShop()}
 * / `getAccessTokenForMainAccount()` / `refreshAccessTokenForShop()` /
 * `refreshAccessTokenForMerchant()}. Despite being grouped under "Public" in
 * Shopee's API Reference, they're an OAuth concern in this SDK, not a
 * `PublicApi` one — don't re-implement them here.
 *
 * Every endpoint in this class signs at the **Public** level (no
 * `access_token`/`shop_id` at all).
 *
 * @see .shopee-docs/API Reference/Public
 * @see .docs/api/Public
 */
final class PublicApi
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Recover an access/refresh token pair using a resend code (obtained by
     * the seller from the shop authorization management page) — for use
     * when the original code/token was lost. Live environment only, not
     * available in the sandbox.
     *
     * @param array<string, mixed> $params resend_code (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Public/get_token_by_resend_code.md
     */
    public function getTokenByResendCode(array $params): array
    {
        return $this->client->public('POST', '/api/v2/public/get_token_by_resend_code', $params);
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Public/get_shopee_ip_ranges.md
     */
    public function getShopeeIpRanges(array $params = []): array
    {
        return $this->client->public('GET', '/api/v2/public/get_shopee_ip_ranges', $params);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size (both optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Public/get_merchants_by_partner.md
     */
    public function getMerchantsByPartner(array $params = []): array
    {
        return $this->client->public('GET', '/api/v2/public/get_merchants_by_partner', $params);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size (both optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Public/get_shops_by_partner.md
     */
    public function getShopsByPartner(array $params = []): array
    {
        return $this->client->public('GET', '/api/v2/public/get_shops_by_partner', $params);
    }
}
