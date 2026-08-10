<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Fbs API domain (`v2.fbs.*`) — Brazil-region Fulfilled-by-Shopee status
 * checks: shop enrollment eligibility, invoice-failure blocking at both
 * shop and SKU level, and failed invoice issuance detail. All four
 * endpoints are read-only GET queries; every response error set includes
 * `fbs_err_region_not_br`, confirming this domain is BR-specific.
 *
 * Every endpoint signs at the **Shop** level.
 *
 * @see .shopee-docs/API Reference/Fbs
 * @see .docs/api/Fbs
 */
final class Fbs
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Fbs/query_br_shop_enrollment_status.md
     */
    public function queryBrShopEnrollmentStatus(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/fbs/query_br_shop_enrollment_status', $params);
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Fbs/query_br_shop_block_status.md
     */
    public function queryBrShopBlockStatus(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/fbs/query_br_shop_block_status', $params);
    }

    /**
     * @param array<string, mixed> $params shop_sku_id (required, `itemID_modelID`).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Fbs/query_br_sku_block_status.md
     */
    public function queryBrSkuBlockStatus(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/fbs/query_br_sku_block_status', $params);
    }

    /**
     * Covers failed invoice issuance across Inbound Requests, RTS Requests,
     * Sales Orders, and Move Transfer Orders.
     *
     * @param array<string, mixed> $params page_no, page_size (both optional, max page_size 100).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Fbs/query_br_shop_invoice_error.md
     */
    public function queryBrShopInvoiceError(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/fbs/query_br_shop_invoice_error', $params);
    }
}
