<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Video;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Video\Analytics;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class AnalyticsTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getOverviewPerformance' => ['getOverviewPerformance', 'GET', '/api/v2/video/get_overview_performance', ['period_type' => 'Day', 'end_date' => '2026-01-01']],
            'getMetricTrend' => ['getMetricTrend', 'GET', '/api/v2/video/get_metric_trend', ['period_type' => 'Day', 'end_date' => '2026-01-01']],
            'getUserDemographics' => ['getUserDemographics', 'GET', '/api/v2/video/get_user_demographics', []],
            'getVideoPerformanceList' => ['getVideoPerformanceList', 'GET', '/api/v2/video/get_video_performance_list', ['page_no' => 1, 'page_size' => 20, 'period_type' => 'Day', 'end_date' => '2026-01-01', 'order_by' => 'Views', 'sort' => 'desc']],
            'getProductPerformanceList' => ['getProductPerformanceList', 'GET', '/api/v2/video/get_prodcut_performance_list', ['page_no' => 1, 'page_size' => 20, 'period_type' => 'Day', 'end_date' => '2026-01-01', 'order_by' => 'PlacedOrders', 'sort' => 'desc']],
            'getVideoDetailPerformance' => ['getVideoDetailPerformance', 'GET', '/api/v2/video/get_video_detail_performance', ['post_id' => 'p1']],
            'getVideoDetailMetricTrend' => ['getVideoDetailMetricTrend', 'GET', '/api/v2/video/get_video_detail_metric_trend', ['post_id' => 'p1', 'metric_name' => 'Views']],
            'getVideoDetailAudienceDistribution' => ['getVideoDetailAudienceDistribution', 'GET', '/api/v2/video/get_video_detail_audience_distribution', ['post_id' => 'p1']],
            'getVideoDetailProductPerformance' => ['getVideoDetailProductPerformance', 'GET', '/api/v2/video/get_video_detail_product_performance', ['page_no' => 1, 'page_size' => 20, 'post_id' => 'p1']],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, userId: 987654));

        (new Analytics($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
