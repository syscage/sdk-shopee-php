<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Ams;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Cross-cutting reporting spanning both campaign types (shop/product/
 * affiliate/content-level metrics, raw conversion/validation rows). Each
 * campaign type's own performance endpoint stays on that campaign's own
 * sub-accessor instead ({@see OpenCampaign::getPerformance()},
 * {@see TargetedCampaign::getPerformance()}) since it's consumed as that
 * feature's own performance tab, not a cross-cutting report. Every
 * `period_type`/`start_date`/`end_date` call here should be validated
 * against {@see getDataUpdateTime()}'s `last_report_date` first.
 *
 * All endpoints here are synchronous — none of Ams's reporting is async
 * (the domain's only async pattern lives in {@see OpenCampaign}'s bulk
 * write operations).
 *
 * @see .shopee-docs/API Reference/Ams
 * @see .docs/api/Ams/Performance
 */
final class Performance
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Single shop-wide aggregate object, no drill-down key.
     *
     * @param array<string, mixed> $params period_type, start_date,
     *     end_date, order_type, channel (all required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_shop_performance.md
     */
    public function getShopPerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_shop_performance', $params);
    }

    /**
     * Keyed `item_id`, across both campaign types (contrast with
     * {@see OpenCampaign::getPerformance()}, which is Open-Campaign-only).
     *
     * @param array<string, mixed> $params period_type, start_date,
     *     end_date, page_no, page_size, order_type, channel (all required);
     *     item_id (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_product_performance.md
     */
    public function getProductPerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_product_performance', $params);
    }

    /**
     * @param array<string, mixed> $params period_type, start_date,
     *     end_date, page_no, page_size, order_type, channel (all required);
     *     affiliate_id (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_affiliate_performance.md
     */
    public function getAffiliatePerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_affiliate_performance', $params);
    }

    /**
     * Keyed `content_id`; `channel` limited to ShopeeVideo/LiveStreaming.
     *
     * @param array<string, mixed> $params period_type, start_date,
     *     end_date, page_no, page_size, order_type, channel (all required);
     *     affiliate_id, item_id (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_content_performance.md
     */
    public function getContentPerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_content_performance', $params);
    }

    /**
     * Shop-wide, split only into open-campaign vs targeted-campaign
     * aggregate buckets — no per-campaign drill-down (use
     * {@see \Syscage\Sdk\Shopee\Core\Http\Api\Ams\TargetedCampaign::getPerformance()}
     * for that).
     *
     * @param array<string, mixed> $params period_type, start_date,
     *     end_date (all required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_campaign_key_metrics_performance.md
     */
    public function getCampaignKeyMetricsPerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_campaign_key_metrics_performance', $params);
    }

    /**
     * Raw order/item/commission rows (not aggregated); `page_no * page_size`
     * must not exceed 10000.
     *
     * @param array<string, mixed> $params page_no, page_size (both
     *     required); order_sn, affiliate_id, item_id, item_name,
     *     l1/l2/l3_category_id, order_status, verified_status, buyer_status,
     *     attr_campaign_id, campaign_partner, seller_campaign_type,
     *     deduction_status, deduction_method, and time-range filters (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_conversion_report.md
     */
    public function getConversionReport(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_conversion_report', $params);
    }

    /**
     * Monthly billing summary, one level up from {@see getValidationReport()}.
     *
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_validation_list.md
     */
    public function getValidationList(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_validation_list', $params);
    }

    /**
     * Order-level drill-down of one billing entry (`validation_id`/`validation_month`).
     *
     * @param array<string, mixed> $params page_no, page_size, validation_id,
     *     validation_month, campaign_source, place_order_time_start,
     *     place_order_time_end (all required); order_sn, l1/l2/l3_category_id,
     *     item_id, item_name, verified_status, attr_campaign_id (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_validation_report.md
     */
    public function getValidationReport(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_validation_report', $params);
    }

    /**
     * The `last_report_date` every other `*_performance`/report endpoint's
     * date range should be validated against.
     *
     * @param array<string, mixed> $params marker_type (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_performance_data_update_time.md
     */
    public function getDataUpdateTime(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_performance_data_update_time', $params);
    }
}
