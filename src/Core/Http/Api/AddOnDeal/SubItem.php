<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\AddOnDeal;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Sub (add-on) items of an add-on deal — the discounted, browsable catalog
 * a buyer can add once a {@see MainItem main item} is purchased. Unlike
 * main items, sub items carry pricing/variant fields (`model_id`,
 * `sub_item_input_price`, `sub_item_limit`).
 *
 * Accessed via `$shopee->addOnDeal()->subItem()`.
 *
 * @see .shopee-docs/API Reference/Add-On Deal (add_add_on_deal_sub_item, update_add_on_deal_sub_item, delete_add_on_deal_sub_item, get_add_on_deal_sub_item)
 * @see .docs/api/AddOnDeal/SubItem.md
 */
final class SubItem
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Add-On Deal/add_add_on_deal_sub_item.md
     * @param array<string, mixed> $params add_on_deal_id, sub_item_list (required): [{item_id, model_id, sub_item_input_price, sub_item_limit, status}].
     * @return array<string, mixed>
     */
    public function addAddOnDealSubItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/add_on_deal/add_add_on_deal_sub_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Add-On Deal/update_add_on_deal_sub_item.md
     * @param array<string, mixed> $params add_on_deal_id, sub_item_list (required): [{item_id, model_id, sub_item_input_price, sub_item_limit, status}].
     * @return array<string, mixed>
     */
    public function updateAddOnDealSubItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/add_on_deal/update_add_on_deal_sub_item', $params);
    }

    /**
     * Note: unlike {@see MainItem::deleteAddOnDealMainItem()}, this stays
     * object-shaped rather than a flat id list — a `model_id` is needed to
     * disambiguate which variant of an item to remove.
     *
     * @see .shopee-docs/API Reference/Add-On Deal/delete_add_on_deal_sub_item.md
     * @param array<string, mixed> $params add_on_deal_id, sub_item_list (required): [{item_id, model_id}].
     * @return array<string, mixed>
     */
    public function deleteAddOnDealSubItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/add_on_deal/delete_add_on_deal_sub_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Add-On Deal/get_add_on_deal_sub_item.md
     * @param array<string, mixed> $params add_on_deal_id (required).
     * @return array<string, mixed>
     */
    public function getAddOnDealSubItem(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/add_on_deal/get_add_on_deal_sub_item', $params);
    }
}
