<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\Product\KitItem;
use Syscage\Sdk\Shopee\Core\Http\Api\Product\Outlet;
use Syscage\Sdk\Shopee\Core\Http\Api\Product\VehicleCompatibility;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Product API domain.
 *
 * Covers item CRUD, stock & price, models/variations, category/attribute/brand
 * lookups, comments, content compliance, and async batch item operations.
 *
 * Kit items, outlet/mart publishing, and vehicle compatibility (Brazil auto
 * parts) are distinct enough sub-features that they live behind their own
 * accessors — {@see kitItem()}, {@see outlet()}, {@see vehicleCompatibility()}
 * — instead of bloating this class further.
 *
 * @see .shopee-docs/API Reference/Product
 * @see .docs/api/Product
 */
final class Product
{
    private ?KitItem $kitItem = null;
    private ?Outlet $outlet = null;
    private ?VehicleCompatibility $vehicleCompatibility = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function kitItem(): KitItem
    {
        return $this->kitItem ??= new KitItem($this->client);
    }

    public function outlet(): Outlet
    {
        return $this->outlet ??= new Outlet($this->client);
    }

    public function vehicleCompatibility(): VehicleCompatibility
    {
        return $this->vehicleCompatibility ??= new VehicleCompatibility($this->client);
    }

    // ------------------------------------------------------------------
    // Item Core
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/Product/add_item.md
     * @param array<string, mixed> $params item_name, description, weight, category_id, logistic_info, image, original_price (required); ~25 optional fields (attribute_list, item_sku, brand, tax_info, size_chart_info, ...).
     * @return array<string, mixed>
     */
    public function addItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/add_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/update_item.md
     * @param array<string, mixed> $params item_id (required); any subset of add_item's optional fields to patch.
     * @return array<string, mixed>
     */
    public function updateItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/update_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/delete_item.md
     * @param array<string, mixed> $params item_id (required).
     * @return array<string, mixed>
     */
    public function deleteItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/delete_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_item_base_info.md
     * @param array<string, mixed> $params item_id_list (required, max 50), need_tax_info, need_complaint_policy (optional).
     * @return array<string, mixed>
     */
    public function getItemBaseInfo(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_item_base_info', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_item_list.md
     * @param array<string, mixed> $params offset, page_size (max 100), item_status (required); update_time_from, update_time_to (optional).
     * @return array<string, mixed>
     */
    public function getItemList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_item_list', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/search_item.md
     * @param array<string, mixed> $params page_size (required); offset, item_name, item_sku, item_status, deboost_only (optional).
     * @return array<string, mixed>
     */
    public function searchItem(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/search_item', $params);
    }

    /**
     * SIP: get direct-shop items published from a main (primary) item.
     *
     * @see .shopee-docs/API Reference/Product/get_direct_item_list.md
     * @param array<string, mixed> $params main_item_id (int64[], required).
     * @return array<string, mixed>
     */
    public function getDirectItemList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_direct_item_list', $params);
    }

    /**
     * SIP: get main-shop items linked to a direct-shop item.
     *
     * @see .shopee-docs/API Reference/Product/get_main_item_list.md
     * @param array<string, mixed> $params direct_item_id (int64[], required).
     * @return array<string, mixed>
     */
    public function getMainItemList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_main_item_list', $params);
    }

    /**
     * SIP: get affiliate-shop items mapped from a primary item id.
     *
     * @see .shopee-docs/API Reference/Product/get_aitem_by_pitem_id.md
     * @param array<string, mixed> $params pitem_id (required).
     * @return array<string, mixed>
     */
    public function getAitemByPitemId(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_aitem_by_pitem_id', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/unlist_item.md
     * @param array<string, mixed> $params item_list (required, 1-50): [{item_id, unlist}].
     * @return array<string, mixed>
     */
    public function unlistItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/unlist_item', $params);
    }

    // ------------------------------------------------------------------
    // Stock & Price
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/Product/update_price.md
     * @param array<string, mixed> $params item_id, price_list (required, 1-50): [{model_id, original_price}].
     * @return array<string, mixed>
     */
    public function updatePrice(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/update_price', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/update_stock.md
     * @param array<string, mixed> $params item_id, stock_list (required, 1-50): [{model_id, seller_stock[]}].
     * @return array<string, mixed>
     */
    public function updateStock(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/update_stock', $params);
    }

    /**
     * SIP: set a primary item's per-affiliate-shop price override.
     *
     * @see .shopee-docs/API Reference/Product/update_sip_item_price.md
     * @param array<string, mixed> $params item_id (required); sip_item_price (optional): [{model_id, sip_item_price}].
     * @return array<string, mixed>
     */
    public function updateSipItemPrice(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/update_sip_item_price', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_direct_shop_recommended_price.md
     * @param array<string, mixed> $params main_item_id, direct_shop_regions (required); category_id, model_list, enabled_channel_id_list (optional).
     * @return array<string, mixed>
     */
    public function getDirectShopRecommendedPrice(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_direct_shop_recommended_price', $params);
    }

    /**
     * Brazil shops only.
     *
     * @see .shopee-docs/API Reference/Product/get_weight_recommendation.md
     * @param array<string, mixed> $params item_name, cover_image_id, category_id, attribute_list, brand_id, description_type (required); description, description_info (optional).
     * @return array<string, mixed>
     */
    public function getWeightRecommendation(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/get_weight_recommendation', $params);
    }

    // ------------------------------------------------------------------
    // Model & Variation
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/Product/add_model.md
     * @param array<string, mixed> $params item_id, model_list (required): [{tier_index, original_price, model_sku, seller_stock, ...}].
     * @return array<string, mixed>
     */
    public function addModel(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/add_model', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/update_model.md
     * @param array<string, mixed> $params item_id, model (required, 1-50): [{model_id, model_sku, model_status, weight, dimension, ...}].
     * @return array<string, mixed>
     */
    public function updateModel(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/update_model', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/delete_model.md
     * @param array<string, mixed> $params item_id, model_id (required).
     * @return array<string, mixed>
     */
    public function deleteModel(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/delete_model', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_model_list.md
     * @param array<string, mixed> $params item_id (required).
     * @return array<string, mixed>
     */
    public function getModelList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_model_list', $params);
    }

    /**
     * Create or reset an item's tier-variation structure (max 2 tiers).
     *
     * @see .shopee-docs/API Reference/Product/init_tier_variation.md
     * @param array<string, mixed> $params item_id, model (required, max 50); standardise_tier_variation (optional).
     * @return array<string, mixed>
     */
    public function initTierVariation(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/init_tier_variation', $params);
    }

    /**
     * Add/remove tier options or update option images — does not change tier structure.
     *
     * @see .shopee-docs/API Reference/Product/update_tier_variation.md
     * @param array<string, mixed> $params item_id (required); model_list, standardise_tier_variation (optional).
     * @return array<string, mixed>
     */
    public function updateTierVariation(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/update_tier_variation', $params);
    }

    /**
     * Get Shopee's standardized tier-variation tree for a category.
     *
     * Note: despite the `v2.product.get_variations` API name, the underlying
     * path is `/api/v2/product/get_variation_tree` (Shopee's own doc mismatch).
     *
     * @see .shopee-docs/API Reference/Product/get_variations.md
     * @param array<string, mixed> $params category_id (required).
     * @return array<string, mixed>
     */
    public function getVariations(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_variation_tree', $params);
    }

    /**
     * Get Unpackaged SKU ids (logistics channel 30029).
     *
     * @see .shopee-docs/API Reference/Product/search_unpackaged_model_list.md
     * @param array<string, mixed> $params page_size (required, 1-48); cursor, item_id, item_name, model_id, unpackaged_sku_id (optional).
     * @return array<string, mixed>
     */
    public function searchUnpackagedModelList(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/search_unpackaged_model_list', $params);
    }

    // ------------------------------------------------------------------
    // Category, Attribute & Brand
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/Product/get_category.md
     * @param array<string, mixed> $params language (optional).
     * @return array<string, mixed>
     */
    public function getCategory(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_category', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_attribute_tree.md
     * @param array<string, mixed> $params category_id_list (required, max 20); language (optional).
     * @return array<string, mixed>
     */
    public function getAttributeTree(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_attribute_tree', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_recommend_attribute.md
     * @param array<string, mixed> $params item_name, category_id (required); cover_image_id (optional).
     * @return array<string, mixed>
     */
    public function getRecommendAttribute(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_recommend_attribute', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/category_recommend.md
     * @param array<string, mixed> $params item_name (required); product_cover_image (optional).
     * @return array<string, mixed>
     */
    public function categoryRecommend(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/category_recommend', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_brand_list.md
     * @param array<string, mixed> $params offset, page_size (max 100), category_id, status (required); language (optional).
     * @return array<string, mixed>
     */
    public function getBrandList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_brand_list', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/register_brand.md
     * @param array<string, mixed> $params original_brand_name, category_list, product_image, brand_region (required); brand_website, brand_description, licenses, ... (optional).
     * @return array<string, mixed>
     */
    public function registerBrand(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/register_brand', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/search_attribute_value_list.md
     * @param array<string, mixed> $params attribute_id, cursor, limit (max 100) (required); value_name (optional).
     * @return array<string, mixed>
     */
    public function searchAttributeValueList(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/search_attribute_value_list', $params);
    }

    /**
     * Local shops only.
     *
     * @see .shopee-docs/API Reference/Product/get_size_chart_list.md
     * @param array<string, mixed> $params category_id, page_size (max 50) (required); cursor (optional).
     * @return array<string, mixed>
     */
    public function getSizeChartList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_size_chart_list', $params);
    }

    /**
     * Local shops only.
     *
     * @see .shopee-docs/API Reference/Product/get_size_chart_detail.md
     * @param array<string, mixed> $params size_chart_id (required).
     * @return array<string, mixed>
     */
    public function getSizeChartDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_size_chart_detail', $params);
    }

    /**
     * Philippines shops.
     *
     * @see .shopee-docs/API Reference/Product/get_product_certification_rule.md
     * @param array<string, mixed> $params attribute_list, category_id (both optional).
     * @return array<string, mixed>
     */
    public function getProductCertificationRule(array $params = []): array
    {
        return $this->client->shop('POST', '/api/v2/product/get_product_certification_rule', $params);
    }

    // ------------------------------------------------------------------
    // Comment
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/Product/get_comment.md
     * @param array<string, mixed> $params cursor, page_size (1-100) (required); item_id, comment_id (optional).
     * @return array<string, mixed>
     */
    public function getComment(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_comment', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/reply_comment.md
     * @param array<string, mixed> $params comment_list (required, 1-100): [{comment_id, comment}].
     * @return array<string, mixed>
     */
    public function replyComment(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/reply_comment', $params);
    }

    // ------------------------------------------------------------------
    // Content & Compliance
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/Product/get_item_violation_info.md
     * @param array<string, mixed> $params item_id_list (required, max 50).
     * @return array<string, mixed>
     */
    public function getItemViolationInfo(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_item_violation_info', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_item_content_diagnosis_result.md
     * @param array<string, mixed> $params item_id_list (required, 1-48).
     * @return array<string, mixed>
     */
    public function getItemContentDiagnosisResult(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/get_item_content_diagnosis_result', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_item_list_by_content_diagnosis.md
     * @param array<string, mixed> $params page_size (max 48) (required); offset, quality_level, issue_type (optional).
     * @return array<string, mixed>
     */
    public function getItemListByContentDiagnosis(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/get_item_list_by_content_diagnosis', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_item_limit.md
     * @param array<string, mixed> $params category_id (optional).
     * @return array<string, mixed>
     */
    public function getItemLimit(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_item_limit', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_item_promotion.md
     * @param array<string, mixed> $params item_id_list (required, 1-50).
     * @return array<string, mixed>
     */
    public function getItemPromotion(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_item_promotion', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_item_extra_info.md
     * @param array<string, mixed> $params item_id_list (required, max 50).
     * @return array<string, mixed>
     */
    public function getItemExtraInfo(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_item_extra_info', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/boost_item.md
     * @param array<string, mixed> $params item_id_list (required, 1-5).
     * @return array<string, mixed>
     */
    public function boostItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/boost_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_boosted_list.md
     * @return array<string, mixed>
     */
    public function getBoostedList(): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_boosted_list');
    }

    // ------------------------------------------------------------------
    // Batch & Async
    // ------------------------------------------------------------------

    /**
     * Async variant of {@see addItem()} for up to 100 items at once.
     * Poll {@see getBatchTaskResult()} with task_type 4 for the result.
     *
     * @see .shopee-docs/API Reference/Product/batch_add_item.md
     * @param array<string, mixed> $params item_list (required, 1-100): same shape as addItem() per item.
     * @return array<string, mixed>
     */
    public function batchAddItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/batch_add_item', $params);
    }

    /**
     * Poll the result of an async batch job. task_type: 1=update_price,
     * 2=update_stock, 3=publish_item_to_outlet_shop, 4=add_item.
     *
     * @see .shopee-docs/API Reference/Product/get_batch_task_result.md
     * @param array<string, mixed> $params task_type, task_id (required).
     * @return array<string, mixed>
     */
    public function getBatchTaskResult(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_batch_task_result', $params);
    }
}
