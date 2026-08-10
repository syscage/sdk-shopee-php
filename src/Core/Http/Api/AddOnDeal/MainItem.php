<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\AddOnDeal;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Main (trigger) items of an add-on deal — buying one unlocks the deal's
 * discounted {@see SubItem sub items}. Main items only ever carry
 * `{item_id, status}`; they are never priced.
 *
 * Accessed via `$shopee->addOnDeal()->mainItem()`.
 *
 * @see .shopee-docs/API Reference/Add-On Deal (add_add_on_deal_main_item, update_add_on_deal_main_item, delete_add_on_deal_main_item, get_add_on_deal_main_item)
 * @see .docs/api/AddOnDeal/MainItem.md
 */
final class MainItem
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Add-On Deal/add_add_on_deal_main_item.md
     * @param array<string, mixed> $params add_on_deal_id, main_item_list (required): [{item_id, status}] (status: 1=enable, 2=disable).
     * @return array<string, mixed>
     */
    public function addAddOnDealMainItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/add_on_deal/add_add_on_deal_main_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Add-On Deal/update_add_on_deal_main_item.md
     * @param array<string, mixed> $params add_on_deal_id, main_item_list (required): [{item_id, status}].
     * @return array<string, mixed>
     */
    public function updateAddOnDealMainItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/add_on_deal/update_add_on_deal_main_item', $params);
    }

    /**
     * Note: unlike {@see \Syscage\Sdk\Shopee\Core\Http\Api\AddOnDeal\SubItem::deleteAddOnDealSubItem()},
     * this takes a flat list of item ids, not objects — main items have no
     * variant/model dimension to disambiguate.
     *
     * @see .shopee-docs/API Reference/Add-On Deal/delete_add_on_deal_main_item.md
     * @param array<string, mixed> $params add_on_deal_id (required); main_item_list (required, int[] of item ids).
     * @return array<string, mixed>
     */
    public function deleteAddOnDealMainItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/add_on_deal/delete_add_on_deal_main_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Add-On Deal/get_add_on_deal_main_item.md
     * @param array<string, mixed> $params add_on_deal_id (required).
     * @return array<string, mixed>
     */
    public function getAddOnDealMainItem(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/add_on_deal/get_add_on_deal_main_item', $params);
    }
}
