<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Ams;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * The seller invites specific affiliates to promote specific products at
 * custom rates — distinct from {@see OpenCampaign}'s blanket, any-affiliate
 * mechanism. Has a full CRUD lifecycle (Upcoming/Ongoing/Ended/Cancelled/
 * Draft/Terminating/Terminated/Paused) that Open Campaign never has. Some
 * campaigns are Shopee-managed (`campaign_source = ShopeeManaged`) rather
 * than seller-created, and those can't be queried via
 * {@see getSettings()}.
 *
 * @see .shopee-docs/API Reference/Ams
 * @see .docs/api/Ams/TargetedCampaign
 */
final class TargetedCampaign
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params campaign_name, period_start_time,
     *     period_end_time, seller_message, item_list, affiliate_list (all
     *     required); is_set_budget, budget (optional). Per-item/affiliate
     *     failures are reported in `fail_item_list`/`fail_affiliate_list`.
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/create_new_targeted_campaign.md
     */
    public function create(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ams/create_new_targeted_campaign', $params);
    }

    /**
     * Metadata only — item/affiliate lists use {@see editProductList()}/
     * {@see editAffiliateList()} instead.
     *
     * @param array<string, mixed> $params campaign_id (required);
     *     campaign_name, period_start_time, period_end_time, is_set_budget,
     *     budget (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/update_basic_info_of_targeted_campaign.md
     */
    public function updateBasicInfo(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ams/update_basic_info_of_targeted_campaign', $params);
    }

    /**
     * Synchronous — sets status to Terminating, no task to poll.
     *
     * @param array<string, mixed> $params campaign_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/terminate_targeted_campaign.md
     */
    public function terminate(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ams/terminate_targeted_campaign', $params);
    }

    /**
     * @param array<string, mixed> $params campaign_id, edit_type
     *     (add/delete/update), item_list (all required — item_id required
     *     per entry, rate optional). Failures in `fail_item_list`.
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/edit_product_list_of_targeted_campaign.md
     */
    public function editProductList(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ams/edit_product_list_of_targeted_campaign', $params);
    }

    /**
     * @param array<string, mixed> $params campaign_id, edit_type
     *     (add/delete), affiliate_list (all required). Failures in
     *     `fail_affiliate_list`.
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/edit_affiliate_list_of_targeted_campaign.md
     */
    public function editAffiliateList(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ams/edit_affiliate_list_of_targeted_campaign', $params);
    }

    /**
     * Page-based (not cursor).
     *
     * @param array<string, mixed> $params page_size, page_no (both
     *     required); campaign_id_list, campaign_name, campaign_status,
     *     period_start_time, period_end_time, item_id, item_name (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_targeted_campaign_list.md
     */
    public function getList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_targeted_campaign_list', $params);
    }

    /**
     * Full detail (item_list + affiliate_list). Cannot query
     * ShopeeManaged-source campaigns.
     *
     * @param array<string, mixed> $params campaign_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_targeted_campaign_settings.md
     */
    public function getSettings(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_targeted_campaign_settings', $params);
    }

    /**
     * @param array<string, mixed> $params period_type, start_date,
     *     end_date, page_no, page_size (all required); campaign_id (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_targeted_campaign_performance.md
     */
    public function getPerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_targeted_campaign_performance', $params);
    }

    /**
     * Cursor-paginated. Feeds {@see create()}/{@see editProductList()}.
     *
     * @param array<string, mixed> $params page_size (required); cursor,
     *     sort_by, search_type, search_content (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_targeted_campaign_addable_product_list.md
     */
    public function getAddableProductList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_targeted_campaign_addable_product_list', $params);
    }
}
