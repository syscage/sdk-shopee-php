<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Ads;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * GMS product campaigns — a separate advertising product from
 * {@see ProductCampaign}, not a third bidding mode of it. Creation is
 * gated by {@see checkEligibility()} (effectively one active GMS campaign
 * per shop); items are attached/detached *after* creation via
 * {@see editItems()} rather than fixed at creation; performance endpoints
 * are POST rather than GET. Shopee's docs never spell out what "GMS"
 * stands for, though `roas_target`'s description ties it to "GMV Max"
 * bidding terminology.
 *
 * @see .shopee-docs/API Reference/Ads
 * @see .docs/api/Ads/GmsCampaign
 */
final class GmsCampaign
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/check_create_gms_product_campaign_eligibility.md
     */
    public function checkEligibility(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/ads/check_create_gms_product_campaign_eligibility', $params);
    }

    /**
     * `roas_target`: omit or pass 0 for GMV Max Auto Bidding; pass a value
     * greater than 0 for GMV Max Custom ROAS.
     *
     * @param array<string, mixed> $params start_date, daily_budget (both
     *     required); end_date, reference_id, roas_target (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/create_gms_product_campaign.md
     */
    public function create(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ads/create_gms_product_campaign', $params);
    }

    /**
     * @param array<string, mixed> $params edit_action (required:
     *     change_budget/change_duration/pause/resume/start/change_roas_target);
     *     campaign_id, daily_budget, start_date, end_date, roas_target,
     *     reference_id (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/edit_gms_product_campaign.md
     */
    public function edit(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ads/edit_gms_product_campaign', $params);
    }

    /**
     * Add or remove items from the campaign — items are not specified at
     * {@see create()} time. Removed items later show up in
     * {@see listDeletedItems()}.
     *
     * @param array<string, mixed> $params edit_action, item_id_list (both
     *     required, 1-30 items); campaign_id (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/edit_gms_item_product_campaign.md
     */
    public function editItems(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ads/edit_gms_item_product_campaign', $params);
    }

    /**
     * Max 3-month range, up to 6 months back.
     *
     * @param array<string, mixed> $params start_date, end_date (both
     *     required); campaign_id (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_gms_campaign_performance.md
     */
    public function getCampaignPerformance(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ads/get_gms_campaign_performance', $params);
    }

    /**
     * @param array<string, mixed> $params start_date, end_date (both
     *     required); campaign_id, offset, limit (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_gms_item_performance.md
     */
    public function getItemPerformance(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ads/get_gms_item_performance', $params);
    }

    /**
     * Items the seller removed from a GMS campaign via
     * {@see editItems()}'s remove action.
     *
     * @param array<string, mixed> $params offset, limit (both optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/list_gms_user_deleted_item.md
     */
    public function listDeletedItems(array $params = []): array
    {
        return $this->client->shop('POST', '/api/v2/ads/list_gms_user_deleted_item', $params);
    }
}
