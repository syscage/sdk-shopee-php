<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Ams;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Ams\OpenCampaign;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class OpenCampaignTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addAllProducts' => ['addAllProducts', 'POST', '/api/v2/ams/add_all_products_to_open_campaign', ['commission_rate' => 0.05]],
            'batchAddProducts' => ['batchAddProducts', 'POST', '/api/v2/ams/batch_add_products_to_open_campaign', ['item_id_list' => [1], 'commission_rate' => 0.05]],
            'batchEditProductsSetting' => ['batchEditProductsSetting', 'POST', '/api/v2/ams/batch_edit_products_open_campaign_setting', ['campaign_ids' => [1]]],
            'batchRemoveProductsSetting' => ['batchRemoveProductsSetting', 'POST', '/api/v2/ams/batch_remove_products_open_campaign_setting', ['campaign_ids' => [1]]],
            'editAllProductsSetting' => ['editAllProductsSetting', 'POST', '/api/v2/ams/edit_all_products_open_campaign_setting', []],
            'removeAllProductsSetting' => ['removeAllProductsSetting', 'POST', '/api/v2/ams/remove_all_products_open_campaign_setting', []],
            'getBatchTaskResult' => ['getBatchTaskResult', 'GET', '/api/v2/ams/get_open_campaign_batch_task_result', ['task_id' => 't1']],
            'getAddedProductList' => ['getAddedProductList', 'GET', '/api/v2/ams/get_open_campaign_added_product', ['page_size' => 20]],
            'getNotAddedProductList' => ['getNotAddedProductList', 'GET', '/api/v2/ams/get_open_campaign_not_added_product', ['page_size' => 20]],
            'getPerformance' => ['getPerformance', 'GET', '/api/v2/ams/get_open_campaign_performance', ['period_type' => 'day', 'start_date' => '2026-01-01', 'end_date' => '2026-01-31', 'page_no' => 1, 'page_size' => 20]],
            'getAutoAddNewProductToggleStatus' => ['getAutoAddNewProductToggleStatus', 'GET', '/api/v2/ams/get_auto_add_new_product_toggle_status', []],
            'updateAutoAddNewProductSetting' => ['updateAutoAddNewProductSetting', 'POST', '/api/v2/ams/update_auto_add_new_product_setting', ['open' => true]],
            'getShopSuggestedRate' => ['getShopSuggestedRate', 'GET', '/api/v2/ams/get_shop_suggested_rate', []],
            'batchGetProductsSuggestedRate' => ['batchGetProductsSuggestedRate', 'GET', '/api/v2/ams/batch_get_products_suggested_rate', ['item_id_list' => '1,2']],
            'getOptimizationSuggestionProduct' => ['getOptimizationSuggestionProduct', 'GET', '/api/v2/ams/get_optimization_suggestion_product', ['page_no' => 1, 'page_size' => 20]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new OpenCampaign($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
