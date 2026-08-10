<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\GlobalProduct;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class GlobalProductTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addGlobalItem' => ['addGlobalItem', 'POST', '/api/v2/global_product/add_global_item', ['category_id' => 1, 'global_item_name' => 'x']],
            'updateGlobalItem' => ['updateGlobalItem', 'POST', '/api/v2/global_product/update_global_item', ['global_item_id' => 1]],
            'deleteGlobalItem' => ['deleteGlobalItem', 'POST', '/api/v2/global_product/delete_global_item', ['global_item_id' => 1]],
            'getGlobalItemInfo' => ['getGlobalItemInfo', 'GET', '/api/v2/global_product/get_global_item_info', ['global_item_id_list' => [1]]],
            'getGlobalItemList' => ['getGlobalItemList', 'GET', '/api/v2/global_product/get_global_item_list', ['page_size' => 10]],
            'getGlobalItemId' => ['getGlobalItemId', 'GET', '/api/v2/global_product/get_global_item_id', ['shop_id' => 1, 'item_id_list' => [1]]],
            'getGlobalItemLimit' => ['getGlobalItemLimit', 'GET', '/api/v2/global_product/get_global_item_limit', []],

            'addGlobalModel' => ['addGlobalModel', 'POST', '/api/v2/global_product/add_global_model', ['global_item_id' => 1, 'global_model' => []]],
            'updateGlobalModel' => ['updateGlobalModel', 'POST', '/api/v2/global_product/update_global_model', ['global_item_id' => 1, 'global_model' => []]],
            'deleteGlobalModel' => ['deleteGlobalModel', 'POST', '/api/v2/global_product/delete_global_model', ['global_item_id' => 1, 'global_model_id' => 1]],
            'getGlobalModelList' => ['getGlobalModelList', 'GET', '/api/v2/global_product/get_global_model_list', ['global_item_id' => 1]],
            'initTierVariation' => ['initTierVariation', 'POST', '/api/v2/global_product/init_tier_variation', ['global_item_id' => 1, 'global_model' => []]],
            'updateTierVariation' => ['updateTierVariation', 'POST', '/api/v2/global_product/update_tier_variation', ['global_item_id' => 1]],
            'getVariations' => ['getVariations', 'GET', '/api/v2/global_product/get_variations', ['category_id' => 1]],

            'getCategory' => ['getCategory', 'GET', '/api/v2/global_product/get_category', []],
            'getAttributeTree' => ['getAttributeTree', 'GET', '/api/v2/global_product/get_mtsku_attribute_tree', ['category_id_list' => [1]]],
            'getRecommendAttribute' => ['getRecommendAttribute', 'GET', '/api/v2/global_product/get_recommend_attribute', ['global_item_name' => 'x', 'category_id' => 1]],
            'categoryRecommend' => ['categoryRecommend', 'GET', '/api/v2/global_product/category_recommend', ['global_item_name' => 'x']],
            'getBrandList' => ['getBrandList', 'GET', '/api/v2/global_product/get_brand_list', ['offset' => 0, 'page_size' => 10, 'category_id' => 1, 'status' => 1]],
            'searchGlobalAttributeValueList' => ['searchGlobalAttributeValueList', 'POST', '/api/v2/global_product/search_global_attribute_value_list', ['attribute_id' => 1, 'cursor' => 0, 'limit' => 10]],
            'getSizeChartList' => ['getSizeChartList', 'GET', '/api/v2/global_product/get_size_chart_list', ['category_id' => 1, 'page_size' => 10, 'cursor' => '']],
            'getSizeChartDetail' => ['getSizeChartDetail', 'GET', '/api/v2/global_product/get_size_chart_detail', ['size_chart_id' => 1]],
            'supportSizeChart' => ['supportSizeChart', 'GET', '/api/v2/global_product/support_size_chart', ['category_id' => 1]],
            'updateSizeChart' => ['updateSizeChart', 'POST', '/api/v2/global_product/update_size_chart', ['global_item_id' => 1, 'size_chart' => 'x']],

            'updatePrice' => ['updatePrice', 'POST', '/api/v2/global_product/update_price', ['global_item_id' => 1, 'price_list' => []]],
            'updateStock' => ['updateStock', 'POST', '/api/v2/global_product/update_stock', ['global_item_id' => 1, 'stock_list' => []]],
            'getLocalAdjustmentRate' => ['getLocalAdjustmentRate', 'GET', '/api/v2/global_product/get_local_adjustment_rate', ['shop_id' => 1]],
            'updateLocalAdjustmentRate' => ['updateLocalAdjustmentRate', 'POST', '/api/v2/global_product/update_local_adjustment_rate', ['adjustment_rate' => 1.1, 'shop_id' => 1]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, merchantId: 1001705));

        (new GlobalProduct($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testCallsSignAsMerchantLevel(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, merchantId: 1001705));

        (new GlobalProduct($client))->getCategory();

        $query = $this->lastRequestQuery();
        $this->assertSame('1001705', $query['merchant_id']);
        $this->assertArrayNotHasKey('shop_id', $query);
    }

    public function testPublishingIsLazilyCachedPerInstance(): void
    {
        $globalProduct = new GlobalProduct($this->makeClient([]));

        $this->assertSame($globalProduct->publishing(), $globalProduct->publishing());
    }
}
