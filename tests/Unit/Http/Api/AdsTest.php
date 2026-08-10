<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Ads;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class AdsTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getTotalBalance' => ['getTotalBalance', 'GET', '/api/v2/ads/get_total_balance', []],
            'getShopToggleInfo' => ['getShopToggleInfo', 'GET', '/api/v2/ads/get_shop_toggle_info', []],
            // Path from Shopee's own code examples, not its doc header — see docblock.
            'getRecommendedItemList' => ['getRecommendedItemList', 'GET', '/api/v2/ads/get_rcmd_item_list', []],
            'getRecommendedKeywordList' => ['getRecommendedKeywordList', 'GET', '/api/v2/ads/get_keyword_rcmd_keyword', ['item_id' => 1]],
            'getProductRecommendedRoiTarget' => ['getProductRecommendedRoiTarget', 'GET', '/api/v2/ads/get_product_recommended_roi_target', ['reference_id' => 'r1', 'item_id' => 1]],
            'getCreateProductAdBudgetSuggestion' => ['getCreateProductAdBudgetSuggestion', 'GET', '/api/v2/ads/get_create_product_ad_budget_suggestion', ['reference_id' => 'r1', 'product_selection' => 'manual', 'campaign_placement' => 'all', 'bidding_method' => 'auto']],
            'getAllCpcAdsDailyPerformance' => ['getAllCpcAdsDailyPerformance', 'GET', '/api/v2/ads/get_all_cpc_ads_daily_performance', ['start_date' => '2026-01-01', 'end_date' => '2026-01-31']],
            'getAllCpcAdsHourlyPerformance' => ['getAllCpcAdsHourlyPerformance', 'GET', '/api/v2/ads/get_all_cpc_ads_hourly_performance', ['performance_date' => '2026-01-01']],
            'getAdsFacilShopRate' => ['getAdsFacilShopRate', 'GET', '/api/v2/ads/get_ads_facil_shop_rate', []],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Ads($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testSubAccessorsAreCached(): void
    {
        $ads = new Ads($this->makeClient([]));

        $this->assertSame($ads->productCampaign(), $ads->productCampaign());
        $this->assertSame($ads->gmsCampaign(), $ads->gmsCampaign());
    }
}
