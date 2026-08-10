<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Ads;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Product-level ad campaigns — one `campaign_id` key space shared by both
 * "auto" and "manual" ad types (disambiguated by an `ad_type` field on the
 * list/setting-info responses, not two different resources).
 * {@see createAuto()}/{@see editAuto()} are marked "coming offline soon"
 * in Shopee's own docs; `createManual()` already accepts its own
 * `bidding_method` (auto or manual bidding) — the platform is folding
 * "auto" campaigns into "manual" as a mode rather than keeping them a
 * separate campaign kind, so no further split (e.g. `Auto`/`Manual`
 * sub-classes) is justified here.
 *
 * @see .shopee-docs/API Reference/Ads
 * @see .docs/api/Ads/ProductCampaign
 */
final class ProductCampaign
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Coming offline soon per Shopee's docs — prefer {@see createManual()}.
     *
     * @param array<string, mixed> $params reference_id, budget, start_date
     *     (all required); end_date (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/create_auto_product_ads.md
     */
    public function createAuto(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ads/create_auto_product_ads', $params);
    }

    /**
     * `bidding_method` selects auto-bidding (via `roas_target`) or manual
     * bidding (via `selected_keywords`/`discovery_ads_locations`) within
     * this single campaign type.
     *
     * @param array<string, mixed> $params reference_id, budget, start_date,
     *     bidding_method, item_id (all required); end_date, roas_target,
     *     selected_keywords, discovery_ads_locations, enhanced_cpc,
     *     smart_creative_setting (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/create_manual_product_ads.md
     */
    public function createManual(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ads/create_manual_product_ads', $params);
    }

    /**
     * Coming offline soon per Shopee's docs — prefer {@see editManual()}.
     *
     * @param array<string, mixed> $params reference_id, campaign_id,
     *     edit_action (all required: start/pause/resume/stop/change_budget/
     *     change_duration); budget, start_date, end_date (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/edit_auto_product_ads.md
     */
    public function editAuto(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ads/edit_auto_product_ads', $params);
    }

    /**
     * @param array<string, mixed> $params reference_id, campaign_id,
     *     edit_action (all required: start/pause/resume/stop/delete/
     *     change_budget/change_duration/...); budget, start_date, end_date,
     *     roas_target, discovery_ads_locations, enhanced_cpc,
     *     smart_creative_setting (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/edit_manual_product_ads.md
     */
    public function editManual(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ads/edit_manual_product_ads', $params);
    }

    /**
     * Keyword-level CRUD (add/delete/restore/change_bid_price/
     * change_match_type), scoped to one manual campaign. Not applicable to
     * auto ads — keyword data only exists under manual bidding.
     *
     * @param array<string, mixed> $params reference_id, campaign_id,
     *     selected_keywords (all required — each entry: edit_action,
     *     keyword required, match_type/bid_price_per_click optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/edit_manual_product_ad_keywords.md
     */
    public function editKeywords(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ads/edit_manual_product_ad_keywords', $params);
    }

    /**
     * @param array<string, mixed> $params ad_type, offset, limit (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_product_level_campaign_id_list.md
     */
    public function getIdList(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_product_level_campaign_id_list', $params);
    }

    /**
     * @param array<string, mixed> $params info_type_list, campaign_id_list
     *     (both required, max 100 campaign_ids).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_product_level_campaign_setting_info.md
     */
    public function getSettingInfo(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_product_level_campaign_setting_info', $params);
    }

    /**
     * Max 1 month date range.
     *
     * @param array<string, mixed> $params start_date, end_date,
     *     campaign_id_list (all required, max 100 campaign_ids).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_product_campaign_daily_performance.md
     */
    public function getDailyPerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_product_campaign_daily_performance', $params);
    }

    /**
     * @param array<string, mixed> $params performance_date,
     *     campaign_id_list (both required, max 100 campaign_ids).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_product_campaign_hourly_performance.md
     */
    public function getHourlyPerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_product_campaign_hourly_performance', $params);
    }
}
