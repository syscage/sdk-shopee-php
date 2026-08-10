<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Ads;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Ads\ProductCampaign;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ProductCampaignTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'createAuto' => ['createAuto', 'POST', '/api/v2/ads/create_auto_product_ads', ['reference_id' => 'r1', 'budget' => 10.0, 'start_date' => '2026-01-01']],
            'createManual' => ['createManual', 'POST', '/api/v2/ads/create_manual_product_ads', ['reference_id' => 'r1', 'budget' => 10.0, 'start_date' => '2026-01-01', 'bidding_method' => 'auto', 'item_id' => 1]],
            'editAuto' => ['editAuto', 'POST', '/api/v2/ads/edit_auto_product_ads', ['reference_id' => 'r1', 'campaign_id' => 1, 'edit_action' => 'pause']],
            'editManual' => ['editManual', 'POST', '/api/v2/ads/edit_manual_product_ads', ['reference_id' => 'r1', 'campaign_id' => 1, 'edit_action' => 'pause']],
            'editKeywords' => ['editKeywords', 'POST', '/api/v2/ads/edit_manual_product_ad_keywords', ['reference_id' => 'r1', 'campaign_id' => 1, 'selected_keywords' => [['edit_action' => 'add', 'keyword' => 'shoes']]]],
            'getIdList' => ['getIdList', 'GET', '/api/v2/ads/get_product_level_campaign_id_list', []],
            'getSettingInfo' => ['getSettingInfo', 'GET', '/api/v2/ads/get_product_level_campaign_setting_info', ['info_type_list' => 'common', 'campaign_id_list' => '1']],
            'getDailyPerformance' => ['getDailyPerformance', 'GET', '/api/v2/ads/get_product_campaign_daily_performance', ['start_date' => '2026-01-01', 'end_date' => '2026-01-31', 'campaign_id_list' => '1']],
            'getHourlyPerformance' => ['getHourlyPerformance', 'GET', '/api/v2/ads/get_product_campaign_hourly_performance', ['performance_date' => '2026-01-01', 'campaign_id_list' => '1']],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new ProductCampaign($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
