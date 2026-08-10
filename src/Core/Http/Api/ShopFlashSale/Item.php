<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\ShopFlashSale;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Item management within a shop flash sale — keyed by `flash_sale_id` +
 * `item_id` (+ optional `model_id` for items with variations), with its own
 * status enum (0 disable, 1 enable, 2 delete, 4 system_rejected, 5
 * manual_rejected) distinct from the parent flash sale's own status/type.
 *
 * All add/update/delete calls here require the parent flash sale to be
 * enabled or upcoming (Shopee raises
 * `shop_flash_sale_is_not_enabled_or_upcoming` otherwise), and a flash sale
 * cannot have more than 50 enabled items at once
 * (`shop_flash_sale_exceed_max_item_limit`).
 *
 * @see .shopee-docs/API Reference/ShopFlashSale
 * @see .docs/api/ShopFlashSale/Item
 */
final class Item
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Check which items/categories are eligible for a flash sale before
     * adding them (rating, likes, discount range, stock range, and any
     * blocked/overlapping categories).
     *
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/get_item_criteria.md
     */
    public function getItemCriteria(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/shop_flash_sale/get_item_criteria', $params);
    }

    /**
     * @param array<string, mixed> $params flash_sale_id (required); items
     *     (required array — each entry: item_id, purchase_limit required;
     *     for variation items, models[] with model_id/input_promo_price/stock;
     *     for non-variation items, item_input_promo_price/item_stock instead).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/add_shop_flash_sale_items.md
     */
    public function addItems(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_flash_sale/add_shop_flash_sale_items', $params);
    }

    /**
     * Price/stock can only be changed while a model/item is disabled (or
     * simultaneously re-enabled in the same call) — Shopee rejects price or
     * stock edits on an already-enabled model/item.
     *
     * @param array<string, mixed> $params flash_sale_id (required); items
     *     (required array — each entry: item_id required, plus optional
     *     purchase_limit/models[]/item_status/item_input_promo_price/item_stock).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/update_shop_flash_sale_items.md
     */
    public function updateItems(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_flash_sale/update_shop_flash_sale_items', $params);
    }

    /**
     * Deleting an item deletes all of its models.
     *
     * @param array<string, mixed> $params flash_sale_id, item_ids (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/delete_shop_flash_sale_items.md
     */
    public function deleteItems(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_flash_sale/delete_shop_flash_sale_items', $params);
    }

    /**
     * @param array<string, mixed> $params flash_sale_id, offset, limit (all required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/get_shop_flash_sale_items.md
     */
    public function getItems(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/shop_flash_sale/get_shop_flash_sale_items', $params);
    }
}
