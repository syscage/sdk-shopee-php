<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Product;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Mart-to-outlet-shop item publishing and pricing/stock sync.
 *
 * Accessed via `$shopee->product()->outlet()`.
 *
 * @see .shopee-docs/API Reference/Product (*_outlet_shop, get_mart_item_*)
 * @see .docs/api/Product/Outlet.md
 */
final class Outlet
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Async. Poll {@see Product::getBatchTaskResult()} with task_type 3.
     *
     * @see .shopee-docs/API Reference/Product/batch_publish_item_to_outlet_shop.md
     * @param array<string, mixed> $params item_list (required, 1-100): [{mart_item_id, outlet_shop_id, publish_item}].
     * @return array<string, mixed>
     */
    public function batchPublishItemToOutletShop(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/batch_publish_item_to_outlet_shop', $params);
    }

    /**
     * Synchronous single-item counterpart of {@see batchPublishItemToOutletShop()}.
     *
     * @see .shopee-docs/API Reference/Product/publish_item_to_outlet_shop.md
     * @param array<string, mixed> $params mart_item_id, outlet_shop_id, publish_item (required).
     * @return array<string, mixed>
     */
    public function publishItemToOutletShop(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/publish_item_to_outlet_shop', $params);
    }

    /**
     * Async. Poll {@see Product::getBatchTaskResult()} with task_type 1.
     *
     * @see .shopee-docs/API Reference/Product/batch_update_outlet_price.md
     * @param array<string, mixed> $params item_list (required, 1-100): [{outlet_shop_id, item_id, price_list}].
     * @return array<string, mixed>
     */
    public function batchUpdateOutletPrice(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/batch_update_outlet_price', $params);
    }

    /**
     * Async. Poll {@see Product::getBatchTaskResult()} with task_type 2.
     *
     * @see .shopee-docs/API Reference/Product/batch_update_outlet_stock.md
     * @param array<string, mixed> $params item_list (required, 1-100): [{outlet_shop_id, item_id, stock_list}].
     * @return array<string, mixed>
     */
    public function batchUpdateOutletStock(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/batch_update_outlet_stock', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_mart_item_by_outlet_item_id.md
     * @param array<string, mixed> $params outlet_item_id (required).
     * @return array<string, mixed>
     */
    public function getMartItemByOutletItemId(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/get_mart_item_by_outlet_item_id', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_mart_item_mapping_by_id.md
     * @param array<string, mixed> $params mart_item_id, outlet_shop_id_list (required).
     * @return array<string, mixed>
     */
    public function getMartItemMappingById(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/get_mart_item_mapping_by_id', $params);
    }
}
