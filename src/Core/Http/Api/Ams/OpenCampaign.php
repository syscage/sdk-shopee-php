<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Ams;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * A shop-wide, opt-in affiliate commission mechanism: any affiliate can
 * promote any product the seller has added to Open Campaign, at the
 * commission rate set for it. The three `*All*` endpoints operate on the
 * shop's *entire* eligible catalog with no explicit id list, so they're
 * async — they return a `task_id` to poll via {@see getBatchTaskResult()}.
 * The `batch*` endpoints take an explicit id list (≤50), are bounded, and
 * respond synchronously with per-id success/failure. Suggested-rate and
 * optimization-suggestion endpoints exist to feed decisions into this same
 * commission-rate workflow, so they stay here rather than in
 * {@see \Syscage\Sdk\Shopee\Core\Http\Api\Ams\Performance}.
 *
 * @see .shopee-docs/API Reference/Ams
 * @see .docs/api/Ams/OpenCampaign
 */
final class OpenCampaign
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Async — returns a `task_id` (task_type `batch_add_open_campaigns`);
     * poll via {@see getBatchTaskResult()}.
     *
     * @param array<string, mixed> $params commission_rate (required);
     *     period_start_time, period_end_time (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/add_all_products_to_open_campaign.md
     */
    public function addAllProducts(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ams/add_all_products_to_open_campaign', $params);
    }

    /**
     * Synchronous, ≤50 items; per-item results in `success_list`/`failed_list`.
     *
     * @param array<string, mixed> $params item_id_list, commission_rate
     *     (both required); period_start_time, period_end_time (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/batch_add_products_to_open_campaign.md
     */
    public function batchAddProducts(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ams/batch_add_products_to_open_campaign', $params);
    }

    /**
     * Synchronous, ≤50 campaign ids (one per open-campaign product entry).
     *
     * @param array<string, mixed> $params campaign_ids (required);
     *     commission_rate, period_start_time, period_end_time (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/batch_edit_products_open_campaign_setting.md
     */
    public function batchEditProductsSetting(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ams/batch_edit_products_open_campaign_setting', $params);
    }

    /**
     * Synchronous, ≤50 campaign ids.
     *
     * @param array<string, mixed> $params campaign_ids (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/batch_remove_products_open_campaign_setting.md
     */
    public function batchRemoveProductsSetting(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ams/batch_remove_products_open_campaign_setting', $params);
    }

    /**
     * Async — applies to ALL open-campaign items; returns a `task_id`
     * (task_type `batch_update_open_campaigns`); poll via
     * {@see getBatchTaskResult()}.
     *
     * @param array<string, mixed> $params commission_rate,
     *     period_start_time, period_end_time (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/edit_all_products_open_campaign_setting.md
     */
    public function editAllProductsSetting(array $params = []): array
    {
        return $this->client->shop('POST', '/api/v2/ams/edit_all_products_open_campaign_setting', $params);
    }

    /**
     * Async — removes ALL open-campaign products; returns a `task_id`
     * (task_type `batch_remove_open_campaigns`); poll via
     * {@see getBatchTaskResult()}.
     *
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/remove_all_products_open_campaign_setting.md
     */
    public function removeAllProductsSetting(array $params = []): array
    {
        return $this->client->shop('POST', '/api/v2/ams/remove_all_products_open_campaign_setting', $params);
    }

    /**
     * Poll the result of an `*AllProducts*`/`*AllProductsSetting()` async task.
     *
     * @param array<string, mixed> $params task_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_open_campaign_batch_task_result.md
     */
    public function getBatchTaskResult(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_open_campaign_batch_task_result', $params);
    }

    /**
     * Cursor-paginated.
     *
     * @param array<string, mixed> $params page_size (required); cursor,
     *     sort_by, search_type, search_content (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_open_campaign_added_product.md
     */
    public function getAddedProductList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_open_campaign_added_product', $params);
    }

    /**
     * Cursor-paginated. Eligible products not yet added to Open Campaign.
     *
     * @param array<string, mixed> $params page_size (required); cursor,
     *     sort_by, search_type, search_content (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_open_campaign_not_added_product.md
     */
    public function getNotAddedProductList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_open_campaign_not_added_product', $params);
    }

    /**
     * Item-level performance restricted to Open Campaign — see
     * {@see \Syscage\Sdk\Shopee\Core\Http\Api\Ams\Performance::getProductPerformance()}
     * for the cross-campaign version.
     *
     * @param array<string, mixed> $params period_type, start_date,
     *     end_date, page_no, page_size (all required); item_id (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_open_campaign_performance.md
     */
    public function getPerformance(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_open_campaign_performance', $params);
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_auto_add_new_product_toggle_status.md
     */
    public function getAutoAddNewProductToggleStatus(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_auto_add_new_product_toggle_status', $params);
    }

    /**
     * Auto-enroll newly listed products into Open Campaign.
     *
     * @param array<string, mixed> $params open (required); commission_rate (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/update_auto_add_new_product_setting.md
     */
    public function updateAutoAddNewProductSetting(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/ams/update_auto_add_new_product_setting', $params);
    }

    /**
     * Shop-wide min/max suggested rate, no item key.
     *
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_shop_suggested_rate.md
     */
    public function getShopSuggestedRate(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_shop_suggested_rate', $params);
    }

    /**
     * Per-product min/max suggested rate, ≤20 items.
     *
     * @param array<string, mixed> $params item_id_list (required, comma-separated).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/batch_get_products_suggested_rate.md
     */
    public function batchGetProductsSuggestedRate(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/batch_get_products_suggested_rate', $params);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size (both
     *     required); rcmd_reason_filter (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_optimization_suggestion_product.md
     */
    public function getOptimizationSuggestionProduct(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_optimization_suggestion_product', $params);
    }
}
