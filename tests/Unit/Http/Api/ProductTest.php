<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Product;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

/**
 * Every method is a thin one-line delegation to Client::shop() (already
 * exercised in depth by ClientTest/ShopTest), so the only thing worth
 * verifying per-endpoint here is that each maps to the correct HTTP method
 * and path — a wrong mapping is the one mistake this class could realistically
 * introduce at this volume of endpoints.
 */
final class ProductTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addItem' => ['addItem', 'POST', '/api/v2/product/add_item', ['item_name' => 'x']],
            'updateItem' => ['updateItem', 'POST', '/api/v2/product/update_item', ['item_id' => 1]],
            'deleteItem' => ['deleteItem', 'POST', '/api/v2/product/delete_item', ['item_id' => 1]],
            'getItemBaseInfo' => ['getItemBaseInfo', 'GET', '/api/v2/product/get_item_base_info', ['item_id_list' => [1]]],
            'getItemList' => ['getItemList', 'GET', '/api/v2/product/get_item_list', ['offset' => 0, 'page_size' => 10]],
            'searchItem' => ['searchItem', 'GET', '/api/v2/product/search_item', ['page_size' => 10]],
            'getDirectItemList' => ['getDirectItemList', 'GET', '/api/v2/product/get_direct_item_list', ['main_item_id' => [1]]],
            'getMainItemList' => ['getMainItemList', 'GET', '/api/v2/product/get_main_item_list', ['direct_item_id' => [1]]],
            'getAitemByPitemId' => ['getAitemByPitemId', 'GET', '/api/v2/product/get_aitem_by_pitem_id', ['pitem_id' => 1]],
            'unlistItem' => ['unlistItem', 'POST', '/api/v2/product/unlist_item', ['item_list' => [['item_id' => 1, 'unlist' => true]]]],

            'updatePrice' => ['updatePrice', 'POST', '/api/v2/product/update_price', ['item_id' => 1, 'price_list' => []]],
            'updateStock' => ['updateStock', 'POST', '/api/v2/product/update_stock', ['item_id' => 1, 'stock_list' => []]],
            'updateSipItemPrice' => ['updateSipItemPrice', 'POST', '/api/v2/product/update_sip_item_price', ['item_id' => 1]],
            'getDirectShopRecommendedPrice' => ['getDirectShopRecommendedPrice', 'GET', '/api/v2/product/get_direct_shop_recommended_price', ['main_item_id' => 1, 'direct_shop_regions' => ['SG']]],
            'getWeightRecommendation' => ['getWeightRecommendation', 'POST', '/api/v2/product/get_weight_recommendation', ['item_name' => 'x']],

            'addModel' => ['addModel', 'POST', '/api/v2/product/add_model', ['item_id' => 1, 'model_list' => []]],
            'updateModel' => ['updateModel', 'POST', '/api/v2/product/update_model', ['item_id' => 1, 'model' => []]],
            'deleteModel' => ['deleteModel', 'POST', '/api/v2/product/delete_model', ['item_id' => 1, 'model_id' => 1]],
            'getModelList' => ['getModelList', 'GET', '/api/v2/product/get_model_list', ['item_id' => 1]],
            'initTierVariation' => ['initTierVariation', 'POST', '/api/v2/product/init_tier_variation', ['item_id' => 1, 'model' => []]],
            'updateTierVariation' => ['updateTierVariation', 'POST', '/api/v2/product/update_tier_variation', ['item_id' => 1]],
            'getVariations' => ['getVariations', 'GET', '/api/v2/product/get_variation_tree', ['category_id' => 1]],
            'searchUnpackagedModelList' => ['searchUnpackagedModelList', 'POST', '/api/v2/product/search_unpackaged_model_list', ['page_size' => 10]],

            'getCategory' => ['getCategory', 'GET', '/api/v2/product/get_category', []],
            'getAttributeTree' => ['getAttributeTree', 'GET', '/api/v2/product/get_attribute_tree', ['category_id_list' => [1]]],
            'getRecommendAttribute' => ['getRecommendAttribute', 'GET', '/api/v2/product/get_recommend_attribute', ['item_name' => 'x', 'category_id' => 1]],
            'categoryRecommend' => ['categoryRecommend', 'GET', '/api/v2/product/category_recommend', ['item_name' => 'x']],
            'getBrandList' => ['getBrandList', 'GET', '/api/v2/product/get_brand_list', ['offset' => 0, 'page_size' => 10, 'category_id' => 1, 'status' => 1]],
            'registerBrand' => ['registerBrand', 'POST', '/api/v2/product/register_brand', ['original_brand_name' => 'x']],
            'searchAttributeValueList' => ['searchAttributeValueList', 'POST', '/api/v2/product/search_attribute_value_list', ['attribute_id' => 1, 'cursor' => 0, 'limit' => 10]],
            'getSizeChartList' => ['getSizeChartList', 'GET', '/api/v2/product/get_size_chart_list', ['category_id' => '1', 'page_size' => '10']],
            'getSizeChartDetail' => ['getSizeChartDetail', 'GET', '/api/v2/product/get_size_chart_detail', ['size_chart_id' => 1]],
            'getProductCertificationRule' => ['getProductCertificationRule', 'POST', '/api/v2/product/get_product_certification_rule', []],

            'getComment' => ['getComment', 'GET', '/api/v2/product/get_comment', ['cursor' => '', 'page_size' => 10]],
            'replyComment' => ['replyComment', 'POST', '/api/v2/product/reply_comment', ['comment_list' => []]],

            'getItemViolationInfo' => ['getItemViolationInfo', 'GET', '/api/v2/product/get_item_violation_info', ['item_id_list' => [1]]],
            'getItemContentDiagnosisResult' => ['getItemContentDiagnosisResult', 'POST', '/api/v2/product/get_item_content_diagnosis_result', ['item_id_list' => [1]]],
            'getItemListByContentDiagnosis' => ['getItemListByContentDiagnosis', 'POST', '/api/v2/product/get_item_list_by_content_diagnosis', ['page_size' => 10]],
            'getItemLimit' => ['getItemLimit', 'GET', '/api/v2/product/get_item_limit', []],
            'getItemPromotion' => ['getItemPromotion', 'GET', '/api/v2/product/get_item_promotion', ['item_id_list' => [1]]],
            'getItemExtraInfo' => ['getItemExtraInfo', 'GET', '/api/v2/product/get_item_extra_info', ['item_id_list' => [1]]],
            'boostItem' => ['boostItem', 'POST', '/api/v2/product/boost_item', ['item_id_list' => [1]]],
            'getBoostedList' => ['getBoostedList', 'GET', '/api/v2/product/get_boosted_list', []],

            'batchAddItem' => ['batchAddItem', 'POST', '/api/v2/product/batch_add_item', ['item_list' => []]],
            'getBatchTaskResult' => ['getBatchTaskResult', 'GET', '/api/v2/product/get_batch_task_result', ['task_type' => 4, 'task_id' => 1]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Product($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testSubAccessorsAreLazilyCachedPerInstance(): void
    {
        $product = new Product($this->makeClient([]));

        $this->assertSame($product->kitItem(), $product->kitItem());
        $this->assertSame($product->outlet(), $product->outlet());
        $this->assertSame($product->vehicleCompatibility(), $product->vehicleCompatibility());
    }
}
