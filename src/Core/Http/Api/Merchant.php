<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Merchant API domain — used only by cross-border sellers upgraded to
 * CBSC (CNSC/KRSC). Unlike every other domain implemented so far, these
 * endpoints are Merchant-level: they sign with `merchant_id`, not
 * `shop_id`, via {@see Client::merchant()}.
 *
 * @see .shopee-docs/API Reference/Merchant
 * @see .docs/api/Merchant
 */
final class Merchant
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Merchant/get_merchant_info.md
     * @return array<string, mixed>
     */
    public function getMerchantInfo(): array
    {
        return $this->client->merchant('GET', '/api/v2/merchant/get_merchant_info');
    }

    /**
     * Seller's courier prepaid accounts.
     *
     * @see .shopee-docs/API Reference/Merchant/get_merchant_prepaid_account_list.md
     * @param array<string, mixed> $params page_no, page_size (required, max 10).
     * @return array<string, mixed>
     */
    public function getMerchantPrepaidAccountList(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/merchant/get_merchant_prepaid_account_list', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Merchant/get_merchant_warehouse_list.md
     * @param array<string, mixed> $params cursor, warehouse_type (required: 1=pickup, 2=return).
     * @return array<string, mixed>
     */
    public function getMerchantWarehouseList(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/merchant/get_merchant_warehouse_list', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Merchant/get_merchant_warehouse_location_list.md
     * @return array<string, mixed>
     */
    public function getMerchantWarehouseLocationList(): array
    {
        return $this->client->merchant('GET', '/api/v2/merchant/get_merchant_warehouse_location_list');
    }

    /**
     * Shops authorized to the partner and bound to this merchant.
     *
     * @see .shopee-docs/API Reference/Merchant/get_shop_list_by_merchant.md
     * @param array<string, mixed> $params page_no (required); page_size (required, max 500).
     * @return array<string, mixed>
     */
    public function getShopListByMerchant(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/merchant/get_shop_list_by_merchant', $params);
    }

    /**
     * Note: Shopee's own documentation header lists the path as
     * `/api/v2/merchant/get_warehouse_eligible_shop_list`, but every one of
     * its own request examples (Java/PHP/cURL/Python) calls
     * `/api/v2/merchant/list_shop_by_warehouse` instead — same kind of
     * doc/example mismatch as `Logistics\ServiceableArea::uploadServiceablePolygon()`.
     * This implementation uses the path from the examples, since signing a
     * mismatched path fails outright with "Wrong sign".
     *
     * @see .shopee-docs/API Reference/Merchant/get_warehouse_eligible_shop_list.md
     * @param array<string, mixed> $params warehouse_id, warehouse_type, cursor (required).
     * @return array<string, mixed>
     */
    public function getWarehouseEligibleShopList(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/merchant/list_shop_by_warehouse', $params);
    }
}
