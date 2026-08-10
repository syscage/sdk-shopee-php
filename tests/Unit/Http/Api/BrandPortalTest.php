<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\BrandPortal;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class BrandPortalTest extends TestCase
{
    use InteractsWithMockHttp;

    private const BASE_PARAMS = [
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
        'timezone' => 'Asia/Singapore',
        'granularity' => 'month',
    ];

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getPrincipalLivestreamPerformance' => ['getPrincipalLivestreamPerformance', 'POST', '/api/v2/principal/get_principal_livestream_performance', self::BASE_PARAMS],
            'getPrincipalVideoPerformance' => ['getPrincipalVideoPerformance', 'POST', '/api/v2/principal/get_principal_video_performance', self::BASE_PARAMS],
            'getPrincipalSalesPerformanceDetail' => ['getPrincipalSalesPerformanceDetail', 'POST', '/api/v2/principal/get_principal_sales_performance_detail', self::BASE_PARAMS],
            'getPrincipalAffiliatePerformance' => ['getPrincipalAffiliatePerformance', 'POST', '/api/v2/principal/get_principal_affiliate_performance', self::BASE_PARAMS],
            'getShopAffiliatePerformance' => ['getShopAffiliatePerformance', 'POST', '/api/v2/principal/get_shop_affiliate_performance', self::BASE_PARAMS],
            'getShopLivestreamPerformance' => ['getShopLivestreamPerformance', 'POST', '/api/v2/principal/get_shop_livestream_performance', self::BASE_PARAMS],
            'getShopSalesPerformanceDetail' => ['getShopSalesPerformanceDetail', 'POST', '/api/v2/principal/get_shop_sales_performance_detail', self::BASE_PARAMS],
            'getShopVideoPerformance' => ['getShopVideoPerformance', 'POST', '/api/v2/principal/get_shop_video_performance', self::BASE_PARAMS],
            'getClipVideoPerformance' => ['getClipVideoPerformance', 'POST', '/api/v2/principal/get_clip_video_performance', self::BASE_PARAMS],
            'getSessionLivestreamPerformance' => ['getSessionLivestreamPerformance', 'POST', '/api/v2/principal/get_session_livestream_performance', self::BASE_PARAMS],
            'getContentAffiliatePerformance' => ['getContentAffiliatePerformance', 'POST', '/api/v2/principal/get_content_affiliate_performance', self::BASE_PARAMS],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, principalId: 55001));

        (new BrandPortal($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testCallsSignAsPrincipalLevelNotShopOrMerchantLevel(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, principalId: 55001));

        (new BrandPortal($client))->getPrincipalLivestreamPerformance(self::BASE_PARAMS);

        $query = $this->lastRequestQuery();
        $this->assertSame('55001', $query['principal_id']);
        $this->assertSame('token-123', $query['access_token']);
        $this->assertArrayNotHasKey('shop_id', $query);
        $this->assertArrayNotHasKey('merchant_id', $query);
    }
}
