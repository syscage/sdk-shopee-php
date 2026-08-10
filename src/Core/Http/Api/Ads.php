<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\Ads\GmsCampaign;
use Syscage\Sdk\Shopee\Core\Http\Api\Ads\ProductCampaign;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Ads API domain (`v2.ads.*`) — Shopee's seller advertising/CPC campaign
 * management. Shop-wide/account-level endpoints only (balance, toggles,
 * pre-creation recommendations, shop-wide CPC rollups); campaigns are split
 * into two sub-accessors with genuinely distinct key spaces and lifecycles:
 *
 * - {@see ProductCampaign} (via {@see productCampaign()}) — "auto" and
 *   "manual" product ads share one `campaign_id` key space (disambiguated
 *   by an `ad_type` field, not two different resources); "auto" is being
 *   phased out in favor of "manual"'s own `bidding_method` (both
 *   `create`/`editAuto` are marked "coming offline soon" in Shopee's docs).
 * - {@see GmsCampaign} (via {@see gmsCampaign()}) — a separate advertising
 *   product: creation is gated by an eligibility check (effectively one
 *   active campaign per shop), items are attached/detached from the
 *   campaign *after* creation (unlike product campaigns, where the item is
 *   fixed at creation), and its performance endpoints are POST rather than
 *   GET. Shopee's docs never spell out what "GMS" stands for.
 *
 * Every endpoint signs at the **Shop** level.
 *
 * @see .shopee-docs/API Reference/Ads
 * @see .docs/api/Ads
 */
final class Ads
{
    private ?ProductCampaign $productCampaign = null;
    private ?GmsCampaign $gmsCampaign = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function productCampaign(): ProductCampaign
    {
        return $this->productCampaign ??= new ProductCampaign($this->client);
    }

    public function gmsCampaign(): GmsCampaign
    {
        return $this->gmsCampaign ??= new GmsCampaign($this->client);
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_total_balance.md
     */
    public function getTotalBalance(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_total_balance', $params);
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_shop_toggle_info.md
     */
    public function getShopToggleInfo(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_shop_toggle_info', $params);
    }

    /**
     * Path from Shopee's own code examples, not its doc header (which says
     * `get_recommended_item_list`) — see docblock precedent in
     * `Logistics\ServiceableArea`/`Merchant`/`GlobalProduct` for this
     * pattern; a wrong path fails signing outright, so the example is trusted.
     *
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_recommended_item_list.md
     */
    public function getRecommendedItemList(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_rcmd_item_list', $params);
    }

    /**
     * Path from Shopee's own code examples, not its doc header (which says
     * `get_recommended_keyword_list`) — same doc/example mismatch pattern
     * as {@see getRecommendedItemList()}.
     *
     * @param array<string, mixed> $params item_id (required); input_keyword (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_recommended_keyword_list.md
     */
    public function getRecommendedKeywordList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_keyword_rcmd_keyword', $params);
    }

    /**
     * @param array<string, mixed> $params reference_id, item_id (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_product_recommended_roi_target.md
     */
    public function getProductRecommendedRoiTarget(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_product_recommended_roi_target', $params);
    }

    /**
     * @param array<string, mixed> $params reference_id, product_selection,
     *     campaign_placement, bidding_method (all required); enhanced_cpc,
     *     discovery_ads_location_names, roas_target, item_id (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_create_product_ad_budget_suggestion.md
     */
    public function getCreateProductAdBudgetSuggestion(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_create_product_ad_budget_suggestion', $params);
    }

    /**
     * @param array<string, mixed> $params start_date, end_date (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_all_cpc_ads_daily_performance.md
     */
    public function getAllCpcAdsDailyPerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_all_cpc_ads_daily_performance', $params);
    }

    /**
     * @param array<string, mixed> $params performance_date (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_all_cpc_ads_hourly_performance.md
     */
    public function getAllCpcAdsHourlyPerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_all_cpc_ads_hourly_performance', $params);
    }

    /**
     * The "Ads Facil" program rate for this shop. The doc's own API Path
     * uses the plain-ASCII spelling (no accent) despite its API Name
     * spelling "fácil" with one — used the ASCII path since there's no
     * Request Example to cross-check it against.
     *
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ads/get_ads_fácil_shop_rate.md
     */
    public function getAdsFacilShopRate(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/ads/get_ads_facil_shop_rate', $params);
    }
}
