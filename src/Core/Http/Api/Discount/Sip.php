<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Discount;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * SIP (cross-border) overseas discount rates, set per affiliate-shop region
 * from the primary shop. Unlike ordinary discounts, these are keyed by
 * `region`, not `discount_id`/`item_id`.
 *
 * Accessed via `$shopee->discount()->sip()`. Call using the SIP primary
 * shop's access token/shop_id.
 *
 * @see .shopee-docs/API Reference/Discount (get_sip_discounts, set_sip_discount, delete_sip_discount)
 * @see .docs/api/Discount/Sip.md
 */
final class Sip
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Only regions with an upcoming/ongoing discount are returned.
     *
     * @see .shopee-docs/API Reference/Discount/get_sip_discounts.md
     * @param array<string, mixed> $params region (optional — omit for all regions).
     * @return array<string, mixed>
     */
    public function getSipDiscounts(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/discount/get_sip_discounts', $params);
    }

    /**
     * Sets a flat market-wide discount rate for every item in the given
     * affiliate region. Cannot be edited again within 15 minutes of the
     * last update.
     *
     * @see .shopee-docs/API Reference/Discount/set_sip_discount.md
     * @param array<string, mixed> $params region, sip_discount_rate (required).
     * @return array<string, mixed>
     */
    public function setSipDiscount(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/discount/set_sip_discount', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Discount/delete_sip_discount.md
     * @param array<string, mixed> $params region (required).
     * @return array<string, mixed>
     */
    public function deleteSipDiscount(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/discount/delete_sip_discount', $params);
    }
}
