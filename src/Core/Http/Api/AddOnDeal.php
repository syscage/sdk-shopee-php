<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\AddOnDeal\MainItem;
use Syscage\Sdk\Shopee\Core\Http\Api\AddOnDeal\SubItem;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Add-On Deal API domain — shop-level promotion where buying a qualifying
 * "main item" unlocks discounted "sub items" the buyer can add on.
 *
 * Unlike {@see Discount}/{@see BundleDeal}, main-item and sub-item
 * management are genuinely distinct shapes, not one flat item list: main
 * items only ever carry `{item_id, status}` (they just unlock the deal),
 * while sub items carry `{item_id, model_id, sub_item_input_price,
 * sub_item_limit}` (they're the priced, browsable add-on catalog) — even
 * their delete requests differ (main item delete is a flat int[], sub item
 * delete stays object[] to disambiguate by model_id). That's why they live
 * behind their own accessors — {@see mainItem()}, {@see subItem()} —
 * instead of flat `addAddOnDealItem()`-style methods on this class.
 *
 * @see .shopee-docs/API Reference/Add-On Deal
 * @see .docs/api/AddOnDeal
 */
final class AddOnDeal
{
    private ?MainItem $mainItem = null;
    private ?SubItem $subItem = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function mainItem(): MainItem
    {
        return $this->mainItem ??= new MainItem($this->client);
    }

    public function subItem(): SubItem
    {
        return $this->subItem ??= new SubItem($this->client);
    }

    /**
     * @see .shopee-docs/API Reference/Add-On Deal/add_add_on_deal.md
     * @param array<string, mixed> $params add_on_deal_name, start_time, end_time, promotion_type (required: 0=add-on discount, 1=gift with minimum spend); purchase_min_spend, per_gift_num, promotion_purchase_limit (optional).
     * @return array<string, mixed>
     */
    public function addAddOnDeal(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/add_on_deal/add_add_on_deal', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Add-On Deal/update_add_on_deal.md
     * @param array<string, mixed> $params add_on_deal_id (required); add_on_deal_name, start_time, end_time, purchase_min_spend, per_gift_num, promotion_purchase_limit, sub_item_priority (optional).
     * @return array<string, mixed>
     */
    public function updateAddOnDeal(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/add_on_deal/update_add_on_deal', $params);
    }

    /**
     * Only an upcoming add-on deal can be deleted.
     *
     * @see .shopee-docs/API Reference/Add-On Deal/delete_add_on_deal.md
     * @param array<string, mixed> $params add_on_deal_id (required).
     * @return array<string, mixed>
     */
    public function deleteAddOnDeal(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/add_on_deal/delete_add_on_deal', $params);
    }

    /**
     * Only an ongoing add-on deal can be ended.
     *
     * @see .shopee-docs/API Reference/Add-On Deal/end_add_on_deal.md
     * @param array<string, mixed> $params add_on_deal_id (required).
     * @return array<string, mixed>
     */
    public function endAddOnDeal(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/add_on_deal/end_add_on_deal', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Add-On Deal/get_add_on_deal.md
     * @param array<string, mixed> $params add_on_deal_id (required).
     * @return array<string, mixed>
     */
    public function getAddOnDeal(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/add_on_deal/get_add_on_deal', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Add-On Deal/get_add_on_deal_list.md
     * @param array<string, mixed> $params promotion_status (required); page_no, page_size (optional).
     * @return array<string, mixed>
     */
    public function getAddOnDealList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/add_on_deal/get_add_on_deal_list', $params);
    }
}
