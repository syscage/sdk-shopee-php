<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Ams;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Ams\TargetedCampaign;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class TargetedCampaignTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'create' => ['create', 'POST', '/api/v2/ams/create_new_targeted_campaign', [
                'campaign_name' => 'VIP Launch', 'period_start_time' => 1, 'period_end_time' => 2,
                'seller_message' => 'Promote our new line', 'item_list' => [['item_id' => 1, 'rate' => 0.1]],
                'affiliate_list' => [['affiliate_id' => 1]],
            ]],
            'updateBasicInfo' => ['updateBasicInfo', 'POST', '/api/v2/ams/update_basic_info_of_targeted_campaign', ['campaign_id' => 1]],
            'terminate' => ['terminate', 'POST', '/api/v2/ams/terminate_targeted_campaign', ['campaign_id' => 1]],
            'editProductList' => ['editProductList', 'POST', '/api/v2/ams/edit_product_list_of_targeted_campaign', ['campaign_id' => 1, 'edit_type' => 'add', 'item_list' => [['item_id' => 1]]]],
            'editAffiliateList' => ['editAffiliateList', 'POST', '/api/v2/ams/edit_affiliate_list_of_targeted_campaign', ['campaign_id' => 1, 'edit_type' => 'add', 'affiliate_list' => [['affiliate_id' => 1]]]],
            'getList' => ['getList', 'GET', '/api/v2/ams/get_targeted_campaign_list', ['page_size' => 20, 'page_no' => 1]],
            'getSettings' => ['getSettings', 'GET', '/api/v2/ams/get_targeted_campaign_settings', ['campaign_id' => 1]],
            'getPerformance' => ['getPerformance', 'GET', '/api/v2/ams/get_targeted_campaign_performance', ['period_type' => 'day', 'start_date' => '2026-01-01', 'end_date' => '2026-01-31', 'page_no' => 1, 'page_size' => 20]],
            'getAddableProductList' => ['getAddableProductList', 'GET', '/api/v2/ams/get_targeted_campaign_addable_product_list', ['page_size' => 20]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new TargetedCampaign($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
