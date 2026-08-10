<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * BundleDeal API domain — shop-level "buy N get a discount" bundle
 * promotions. Structurally near-identical to {@see Discount}: CRUD + item
 * management on one class, item management kept alongside the entity
 * rather than split out (an item_list is intrinsic to a bundle deal).
 *
 * @see .shopee-docs/API Reference/BundleDeal
 * @see .docs/api/BundleDeal
 */
final class BundleDeal
{
    public function __construct(private readonly Client $client)
    {
    }

    // ------------------------------------------------------------------
    // Bundle Deal
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/BundleDeal/add_bundle_deal.md
     * @param array<string, mixed> $params rule_type, discount_value, fix_price, discount_percentage, min_amount, start_time, end_time, name, purchase_limit (required — only the field matching rule_type is used); additional_tiers (optional, max 2).
     * @return array<string, mixed>
     */
    public function addBundleDeal(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/bundle_deal/add_bundle_deal', $params);
    }

    /**
     * @see .shopee-docs/API Reference/BundleDeal/update_bundle_deal.md
     * @param array<string, mixed> $params bundle_deal_id (required); all other addBundleDeal() fields optional.
     * @return array<string, mixed>
     */
    public function updateBundleDeal(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/bundle_deal/update_bundle_deal', $params);
    }

    /**
     * Only an upcoming bundle deal can be deleted.
     *
     * @see .shopee-docs/API Reference/BundleDeal/delete_bundle_deal.md
     * @param array<string, mixed> $params bundle_deal_id (required).
     * @return array<string, mixed>
     */
    public function deleteBundleDeal(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/bundle_deal/delete_bundle_deal', $params);
    }

    /**
     * Only an ongoing bundle deal can be ended.
     *
     * @see .shopee-docs/API Reference/BundleDeal/end_bundle_deal.md
     * @param array<string, mixed> $params bundle_deal_id (required).
     * @return array<string, mixed>
     */
    public function endBundleDeal(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/bundle_deal/end_bundle_deal', $params);
    }

    /**
     * @see .shopee-docs/API Reference/BundleDeal/get_bundle_deal.md
     * @param array<string, mixed> $params bundle_deal_id (required).
     * @return array<string, mixed>
     */
    public function getBundleDeal(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/bundle_deal/get_bundle_deal', $params);
    }

    /**
     * @see .shopee-docs/API Reference/BundleDeal/get_bundle_deal_list.md
     * @param array<string, mixed> $params page_size, time_status (1=all,2=upcoming,3=ongoing,4=expired), page_no (all optional).
     * @return array<string, mixed>
     */
    public function getBundleDealList(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/bundle_deal/get_bundle_deal_list', $params);
    }

    // ------------------------------------------------------------------
    // Bundle Deal Items
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/BundleDeal/add_bundle_deal_item.md
     * @param array<string, mixed> $params bundle_deal_id, item_list (required): [{item_id, status}].
     * @return array<string, mixed>
     */
    public function addBundleDealItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/bundle_deal/add_bundle_deal_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/BundleDeal/update_bundle_deal_item.md
     * @param array<string, mixed> $params bundle_deal_id, item_list (required): [{item_id, status}].
     * @return array<string, mixed>
     */
    public function updateBundleDealItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/bundle_deal/update_bundle_deal_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/BundleDeal/delete_bundle_deal_item.md
     * @param array<string, mixed> $params bundle_deal_id, item_list (required): [{item_id}].
     * @return array<string, mixed>
     */
    public function deleteBundleDealItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/bundle_deal/delete_bundle_deal_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/BundleDeal/get_bundle_deal_item.md
     * @param array<string, mixed> $params bundle_deal_id (required).
     * @return array<string, mixed>
     */
    public function getBundleDealItem(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/bundle_deal/get_bundle_deal_item', $params);
    }
}
