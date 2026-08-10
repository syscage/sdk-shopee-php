<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\GlobalProduct\Publishing;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * GlobalProduct API domain — cross-border sellers (CB CNSC/KRSC) manage one
 * "global item" here, which is then published into multiple regional shops.
 *
 * Every endpoint in this domain is Merchant-level (signs with `merchant_id`,
 * via {@see Client::merchant()}), same as {@see Merchant}. Several methods
 * additionally take a `shop_id` as an ordinary request parameter to target a
 * specific regional shop — that is unrelated to auth and doesn't change how
 * the request is signed.
 *
 * Publishing a global item into regional shops is a distinct enough
 * workflow (with its own async job) to live behind its own accessor —
 * {@see publishing()} — instead of bloating this class further.
 *
 * @see .shopee-docs/API Reference/GlobalProduct
 * @see .docs/api/GlobalProduct
 */
final class GlobalProduct
{
    private ?Publishing $publishing = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function publishing(): Publishing
    {
        return $this->publishing ??= new Publishing($this->client);
    }

    // ------------------------------------------------------------------
    // Global Item Core
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/add_global_item.md
     * @param array<string, mixed> $params category_id, global_item_name, description, original_price, weight, pre_order (required); ~15 optional fields (attribute_list, brand, image, size_chart_info, ...).
     * @return array<string, mixed>
     */
    public function addGlobalItem(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/add_global_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/update_global_item.md
     * @param array<string, mixed> $params global_item_id (required); any subset of addGlobalItem()'s optional fields to patch.
     * @return array<string, mixed>
     */
    public function updateGlobalItem(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/update_global_item', $params);
    }

    /**
     * Cascades: also removes every regional item published from it.
     *
     * @see .shopee-docs/API Reference/GlobalProduct/delete_global_item.md
     * @param array<string, mixed> $params global_item_id (required).
     * @return array<string, mixed>
     */
    public function deleteGlobalItem(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/delete_global_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_global_item_info.md
     * @param array<string, mixed> $params global_item_id_list (required, max 20).
     * @return array<string, mixed>
     */
    public function getGlobalItemInfo(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_global_item_info', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_global_item_list.md
     * @param array<string, mixed> $params page_size (required, 1-50); offset, update_time_from, update_time_to (optional).
     * @return array<string, mixed>
     */
    public function getGlobalItemList(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_global_item_list', $params);
    }

    /**
     * Map regional shop item ids to their global_item_id.
     *
     * @see .shopee-docs/API Reference/GlobalProduct/get_global_item_id.md
     * @param array<string, mixed> $params shop_id, item_id_list (required, max 20).
     * @return array<string, mixed>
     */
    public function getGlobalItemId(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_global_item_id', $params);
    }

    /**
     * Upload constraints (price/stock/name/image/days-to-ship/size-chart limits).
     *
     * @see .shopee-docs/API Reference/GlobalProduct/get_global_item_limit.md
     * @param array<string, mixed> $params category_id (optional).
     * @return array<string, mixed>
     */
    public function getGlobalItemLimit(array $params = []): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_global_item_limit', $params);
    }

    // ------------------------------------------------------------------
    // Model & Variation
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/add_global_model.md
     * @param array<string, mixed> $params global_item_id, global_model (required, 1-50).
     * @return array<string, mixed>
     */
    public function addGlobalModel(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/add_global_model', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/update_global_model.md
     * @param array<string, mixed> $params global_item_id, global_model (required, 1-50).
     * @return array<string, mixed>
     */
    public function updateGlobalModel(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/update_global_model', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/delete_global_model.md
     * @param array<string, mixed> $params global_item_id, global_model_id (required).
     * @return array<string, mixed>
     */
    public function deleteGlobalModel(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/delete_global_model', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_global_model_list.md
     * @param array<string, mixed> $params global_item_id (required).
     * @return array<string, mixed>
     */
    public function getGlobalModelList(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_global_model_list', $params);
    }

    /**
     * Initialize or change an item's tier structure (0/1/2 tiers) and create its models.
     *
     * @see .shopee-docs/API Reference/GlobalProduct/init_tier_variation.md
     * @param array<string, mixed> $params global_item_id, global_model (required, max 50); standardise_tier_variation (optional).
     * @return array<string, mixed>
     */
    public function initTierVariation(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/init_tier_variation', $params);
    }

    /**
     * Add/remove tier options or update option images — does not change tier structure.
     *
     * @see .shopee-docs/API Reference/GlobalProduct/update_tier_variation.md
     * @param array<string, mixed> $params global_item_id (required); model_list, standardise_tier_variation (optional).
     * @return array<string, mixed>
     */
    public function updateTierVariation(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/update_tier_variation', $params);
    }

    /**
     * Get Shopee's standardized variation/group/option tree for a leaf category.
     *
     * @see .shopee-docs/API Reference/GlobalProduct/get_variations.md
     * @param array<string, mixed> $params category_id (required).
     * @return array<string, mixed>
     */
    public function getVariations(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_variations', $params);
    }

    // ------------------------------------------------------------------
    // Category, Attribute & Brand
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_category.md
     * @param array<string, mixed> $params language (optional).
     * @return array<string, mixed>
     */
    public function getCategory(array $params = []): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_category', $params);
    }

    /**
     * Note: Shopee's own documentation header lists the path as
     * `/api/v2/global_product/get_attribute_tree`, but every one of its own
     * request examples (Java/PHP/cURL/Python) calls
     * `/api/v2/global_product/get_mtsku_attribute_tree` instead — the same
     * kind of doc/example mismatch as
     * `Logistics\ServiceableArea::uploadServiceablePolygon()` and
     * `Merchant::getWarehouseEligibleShopList()`. This implementation uses
     * the path from the examples, since signing a mismatched path fails
     * outright with "Wrong sign".
     *
     * @see .shopee-docs/API Reference/GlobalProduct/get_attribute_tree.md
     * @param array<string, mixed> $params category_id_list (required, max 20); language (optional).
     * @return array<string, mixed>
     */
    public function getAttributeTree(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_mtsku_attribute_tree', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_recommend_attribute.md
     * @param array<string, mixed> $params global_item_name, category_id (required); cover_image_id (optional).
     * @return array<string, mixed>
     */
    public function getRecommendAttribute(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_recommend_attribute', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/category_recommend.md
     * @param array<string, mixed> $params global_item_name (required); global_product_cover_image (optional).
     * @return array<string, mixed>
     */
    public function categoryRecommend(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/category_recommend', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_brand_list.md
     * @param array<string, mixed> $params offset, page_size, category_id, status (required).
     * @return array<string, mixed>
     */
    public function getBrandList(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_brand_list', $params);
    }

    /**
     * Only searches attributes flagged `support_search_value`.
     *
     * @see .shopee-docs/API Reference/GlobalProduct/search_global_attribute_value_list.md
     * @param array<string, mixed> $params attribute_id, cursor, limit (max 100) (required); value_name (optional).
     * @return array<string, mixed>
     */
    public function searchGlobalAttributeValueList(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/search_global_attribute_value_list', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_size_chart_list.md
     * @param array<string, mixed> $params category_id, page_size, cursor (required).
     * @return array<string, mixed>
     */
    public function getSizeChartList(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_size_chart_list', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_size_chart_detail.md
     * @param array<string, mixed> $params size_chart_id (required); language (optional).
     * @return array<string, mixed>
     */
    public function getSizeChartDetail(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_size_chart_detail', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/support_size_chart.md
     * @param array<string, mixed> $params category_id (required).
     * @return array<string, mixed>
     */
    public function supportSizeChart(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/support_size_chart', $params);
    }

    /**
     * Legacy image-based size chart. Prefer the template-based
     * `size_chart_info` field on {@see addGlobalItem()}/{@see updateGlobalItem()}.
     *
     * @see .shopee-docs/API Reference/GlobalProduct/update_size_chart.md
     * @param array<string, mixed> $params global_item_id, size_chart (required).
     * @return array<string, mixed>
     */
    public function updateSizeChart(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/update_size_chart', $params);
    }

    // ------------------------------------------------------------------
    // Price & Stock
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/update_price.md
     * @param array<string, mixed> $params global_item_id, price_list (required, 1-50).
     * @return array<string, mixed>
     */
    public function updatePrice(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/update_price', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/update_stock.md
     * @param array<string, mixed> $params global_item_id, stock_list (required, 1-50).
     * @return array<string, mixed>
     */
    public function updateStock(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/update_stock', $params);
    }

    /**
     * The cross-border-to-local price multiplier for a regional shop.
     *
     * @see .shopee-docs/API Reference/GlobalProduct/get_local_adjustment_rate.md
     * @param array<string, mixed> $params shop_id (required).
     * @return array<string, mixed>
     */
    public function getLocalAdjustmentRate(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_local_adjustment_rate', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/update_local_adjustment_rate.md
     * @param array<string, mixed> $params adjustment_rate, shop_id (required).
     * @return array<string, mixed>
     */
    public function updateLocalAdjustmentRate(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/update_local_adjustment_rate', $params);
    }
}
