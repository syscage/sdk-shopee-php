<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Ams;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Ams\Performance;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class PerformanceTest extends TestCase
{
    use InteractsWithMockHttp;

    private const PERIOD_PARAMS = ['period_type' => 'day', 'start_date' => '2026-01-01', 'end_date' => '2026-01-31'];

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        $withPaging = [...self::PERIOD_PARAMS, 'page_no' => 1, 'page_size' => 20, 'order_type' => 'gmv', 'channel' => 'all'];

        return [
            'getShopPerformance' => ['getShopPerformance', 'GET', '/api/v2/ams/get_shop_performance', [...self::PERIOD_PARAMS, 'order_type' => 'gmv', 'channel' => 'all']],
            'getProductPerformance' => ['getProductPerformance', 'GET', '/api/v2/ams/get_product_performance', $withPaging],
            'getAffiliatePerformance' => ['getAffiliatePerformance', 'GET', '/api/v2/ams/get_affiliate_performance', $withPaging],
            'getContentPerformance' => ['getContentPerformance', 'GET', '/api/v2/ams/get_content_performance', $withPaging],
            'getCampaignKeyMetricsPerformance' => ['getCampaignKeyMetricsPerformance', 'GET', '/api/v2/ams/get_campaign_key_metrics_performance', self::PERIOD_PARAMS],
            'getConversionReport' => ['getConversionReport', 'GET', '/api/v2/ams/get_conversion_report', ['page_no' => 1, 'page_size' => 20]],
            'getValidationList' => ['getValidationList', 'GET', '/api/v2/ams/get_validation_list', []],
            'getValidationReport' => ['getValidationReport', 'GET', '/api/v2/ams/get_validation_report', [
                'page_no' => 1, 'page_size' => 20, 'validation_id' => 'v1', 'validation_month' => '2026-01',
                'campaign_source' => 'Seller', 'place_order_time_start' => 1, 'place_order_time_end' => 2,
            ]],
            'getDataUpdateTime' => ['getDataUpdateTime', 'GET', '/api/v2/ams/get_performance_data_update_time', ['marker_type' => 1]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Performance($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
