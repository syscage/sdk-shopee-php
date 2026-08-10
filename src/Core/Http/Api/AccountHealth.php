<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * AccountHealth API domain (`v2.account_health.*`) — shop performance
 * metrics, penalty points, punishments, problematic listings, late orders,
 * and per-metric drill-down detail. All read-only GET queries, single
 * class: none of these six warrant a sub-accessor (no shared distinct
 * key space or lifecycle beyond "read-only report").
 *
 * Every endpoint signs at the **Shop** level.
 *
 * @see .shopee-docs/API Reference/AccountHealth
 * @see .docs/api/AccountHealth
 */
final class AccountHealth
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/AccountHealth/get_shop_performance.md
     */
    public function getShopPerformance(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/account_health/get_shop_performance', $params);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size, violation_type (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/AccountHealth/get_penalty_point_history.md
     */
    public function getPenaltyPointHistory(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/account_health/get_penalty_point_history', $params);
    }

    /**
     * @param array<string, mixed> $params punishment_status (required: 1 =
     *     Ongoing, 2 = Ended); page_no, page_size (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/AccountHealth/get_punishment_history.md
     */
    public function getPunishmentHistory(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/account_health/get_punishment_history', $params);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size (both optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/AccountHealth/get_listings_with_issues.md
     */
    public function getListingsWithIssues(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/account_health/get_listings_with_issues', $params);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size (both optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/AccountHealth/get_late_orders.md
     */
    public function getLateOrders(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/account_health/get_late_orders', $params);
    }

    /**
     * Drill-down detail (affected orders / relevant listings / relevant
     * violations) behind a single metric from {@see getShopPerformance()}.
     *
     * @param array<string, mixed> $params metric_id (required); page_no,
     *     page_size (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/AccountHealth/get_metric_source_detail.md
     */
    public function getMetricSourceDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/account_health/get_metric_source_detail', $params);
    }
}
