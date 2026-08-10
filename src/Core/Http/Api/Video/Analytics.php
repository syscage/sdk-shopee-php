<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Video;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Shopee Video performance reporting. Five endpoints are shop-wide/
 * multi-video aggregates (overview, trend, demographics, video list,
 * product list); four are single-video drill-downs keyed by `post_id`
 * (detail performance, detail metric trend, detail audience distribution,
 * detail product performance). Kept as one class — all nine are read-only
 * reports, the "detail" ones are simply a `post_id`-scoped variant of the
 * same reporting concern, not a distinct entity.
 *
 * **Path typo preserved on purpose**: Shopee's own path for the product
 * list endpoint is literally `/api/v2/video/get_prodcut_performance_list`
 * (missing/transposed letters in "product") — confirmed in the doc's own
 * header, API Name, and all of its request examples, and NOT shared by the
 * correctly-spelled sibling `get_video_detail_product_performance`. The
 * PHP method here is named correctly (`getProductPerformanceList()`) since
 * there's no reason to propagate a typo into the public API, but the
 * literal path is used as-is — getting it wrong fails the call outright.
 *
 * Every endpoint signs at the **User** level, same as {@see \Syscage\Sdk\Shopee\Core\Http\Api\Video}.
 *
 * @see .shopee-docs/API Reference/Video
 * @see .docs/api/Video/Analytics
 */
final class Analytics
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params period_type, end_date (both
     *     required; period_type: Day/Week/Month/Last7d/Last15d/Last30d;
     *     end_date must align to period_type, e.g. Week requires a Sunday).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_overview_performance.md
     */
    public function getOverviewPerformance(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_overview_performance', $params);
    }

    /**
     * @param array<string, mixed> $params period_type, end_date (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_metric_trend.md
     */
    public function getMetricTrend(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_metric_trend', $params);
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_user_demographics.md
     */
    public function getUserDemographics(array $params = []): array
    {
        return $this->client->user('GET', '/api/v2/video/get_user_demographics', $params);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size, period_type,
     *     end_date, order_by, sort (all required); caption (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_video_performance_list.md
     */
    public function getVideoPerformanceList(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_video_performance_list', $params);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size, period_type,
     *     end_date, order_by, sort (all required); item_id, item_name (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_prodcut_performance_list.md
     */
    public function getProductPerformanceList(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_prodcut_performance_list', $params);
    }

    /**
     * Lifetime cumulative stats for one video — no date-range params.
     *
     * @param array<string, mixed> $params post_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_video_detail_performance.md
     */
    public function getVideoDetailPerformance(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_video_detail_performance', $params);
    }

    /**
     * @param array<string, mixed> $params post_id, metric_name (both
     *     required; metric_name: Views, Likes, Comments, Shares,
     *     FollowersGrowth, PlacedOrders, PlacedSales, UniqueBuyers,
     *     ConversionRate, SoldItems, SalesPerOrder, SalesPerBuyer).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_video_detail_metric_trend.md
     */
    public function getVideoDetailMetricTrend(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_video_detail_metric_trend', $params);
    }

    /**
     * @param array<string, mixed> $params post_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_video_detail_audience_distribution.md
     */
    public function getVideoDetailAudienceDistribution(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_video_detail_audience_distribution', $params);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size, post_id (all
     *     required); item_id, item_name (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_video_detail_product_performance.md
     */
    public function getVideoDetailProductPerformance(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_video_detail_product_performance', $params);
    }
}
