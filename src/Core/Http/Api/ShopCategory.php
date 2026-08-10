<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * ShopCategory API domain (`v2.shop_category.*`) — a shop's own item
 * collections ("in-shop categories"), plus managing which items belong to
 * each one. Item-list management is kept on this single class rather than
 * split into a sub-accessor: same rationale as {@see Discount}'s item
 * methods — item membership is intrinsic to a category, not a distinct
 * concept with its own key space or lifecycle.
 *
 * Every endpoint signs at the **Shop** level.
 *
 * @see .shopee-docs/API Reference/ShopCategory
 * @see .docs/api/ShopCategory
 */
final class ShopCategory
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params name (required); sort_weight (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopCategory/add_shop_category.md
     */
    public function addShopCategory(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_category/add_shop_category', $params);
    }

    /**
     * @param array<string, mixed> $params shop_category_id (required);
     *     name, sort_weight, status (optional: NORMAL, INACTIVE, DELETED).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopCategory/update_shop_category.md
     */
    public function updateShopCategory(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_category/update_shop_category', $params);
    }

    /**
     * @param array<string, mixed> $params shop_category_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopCategory/delete_shop_category.md
     */
    public function deleteShopCategory(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_category/delete_shop_category', $params);
    }

    /**
     * @param array<string, mixed> $params page_size, page_no (both required per Shopee's doc).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopCategory/get_shop_category_list.md
     */
    public function getShopCategoryList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/shop_category/get_shop_category_list', $params);
    }

    /**
     * @param array<string, mixed> $params shop_category_id, item_list
     *     (both required, max 100 items per request).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopCategory/add_item_list.md
     */
    public function addItemList(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_category/add_item_list', $params);
    }

    /**
     * @param array<string, mixed> $params shop_category_id, item_list
     *     (both required, max 100 items per request).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopCategory/delete_item_list.md
     */
    public function deleteItemList(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_category/delete_item_list', $params);
    }

    /**
     * @param array<string, mixed> $params shop_category_id (required);
     *     page_size, page_no (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopCategory/get_item_list.md
     */
    public function getItemList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/shop_category/get_item_list', $params);
    }
}
