<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * BrandPortal API domain (`v2.principal.*`) — brand-owner analytics for a
 * "principal": a brand entity that owns multiple shops across multiple
 * regions (e.g. one principal with a Malaysia storefront and a Singapore
 * storefront underneath it). All 11 endpoints are synchronous POST reads
 * returning `summary`/`details` metrics, and all live under the same
 * `/api/v2/principal/*` path segment regardless of whether the endpoint
 * name says "principal", "shop", "session", "content", or "clip_video" —
 * those words describe what the query is scoped to (a `shop_list`,
 * `session_list`, etc. filter in the request body), not a different
 * routing module or auth level. Kept as a single class: all 11 share
 * identical shape (date range + granularity in, summary/details out), so
 * there's no genuinely distinct sub-concept to split out.
 *
 * Every endpoint signs at a **fourth auth level, Principal**
 * (`access_token` + `principal_id`, not `shop_id` or `merchant_id`) — see
 * {@see Client::principal()}. `shop_id`/`session_ids`/`content_ids`/
 * `video_ids` appear only as ordinary request-body filters and must each
 * belong to the authenticated `principal_id`; they don't change signing.
 *
 * @see .shopee-docs/API Reference/BrandPortal
 * @see .docs/api/BrandPortal
 */
final class BrandPortal
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); region_list (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_principal_livestream_performance.md
     */
    public function getPrincipalLivestreamPerformance(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_principal_livestream_performance', $params);
    }

    /**
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); region_list (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_principal_video_performance.md
     */
    public function getPrincipalVideoPerformance(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_principal_video_performance', $params);
    }

    /**
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); region_list (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_principal_sales_performance_detail.md
     */
    public function getPrincipalSalesPerformanceDetail(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_principal_sales_performance_detail', $params);
    }

    /**
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); region_list (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_principal_affiliate_performance.md
     */
    public function getPrincipalAffiliatePerformance(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_principal_affiliate_performance', $params);
    }

    /**
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); shop_list, up to 50 shops (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_shop_affiliate_performance.md
     */
    public function getShopAffiliatePerformance(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_shop_affiliate_performance', $params);
    }

    /**
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); shop_list, up to 50 shops (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_shop_livestream_performance.md
     */
    public function getShopLivestreamPerformance(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_shop_livestream_performance', $params);
    }

    /**
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); shop_list, up to 50 shops (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_shop_sales_performance_detail.md
     */
    public function getShopSalesPerformanceDetail(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_shop_sales_performance_detail', $params);
    }

    /**
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); shop_list, up to 50 shops (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_shop_video_performance.md
     */
    public function getShopVideoPerformance(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_shop_video_performance', $params);
    }

    /**
     * Cursor-paginated when `video_list` is omitted/empty: pass the
     * previous response's `next_cursor` back in as `cursor`.
     *
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); video_list (up to 100 video_ids
     *     total), page_size, cursor (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_clip_video_performance.md
     */
    public function getClipVideoPerformance(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_clip_video_performance', $params);
    }

    /**
     * Cursor-paginated when `session_list` is omitted/empty: pass the
     * previous response's `next_cursor` back in as `cursor`.
     *
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); session_list, page_size, cursor (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_session_livestream_performance.md
     */
    public function getSessionLivestreamPerformance(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_session_livestream_performance', $params);
    }

    /**
     * Cursor-paginated when `content_list` is omitted/empty: pass the
     * previous response's `next_cursor` back in as `cursor`.
     *
     * @param array<string, mixed> $params start_date, end_date, timezone,
     *     granularity (all required); content_list, page_size, cursor (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/BrandPortal/get_content_affiliate_performance.md
     */
    public function getContentAffiliatePerformance(array $params): array
    {
        return $this->client->principal('POST', '/api/v2/principal/get_content_affiliate_performance', $params);
    }
}
