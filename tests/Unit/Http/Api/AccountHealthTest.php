<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\AccountHealth;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class AccountHealthTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getShopPerformance' => ['getShopPerformance', 'GET', '/api/v2/account_health/get_shop_performance', []],
            'getPenaltyPointHistory' => ['getPenaltyPointHistory', 'GET', '/api/v2/account_health/get_penalty_point_history', []],
            'getPunishmentHistory' => ['getPunishmentHistory', 'GET', '/api/v2/account_health/get_punishment_history', ['punishment_status' => 1]],
            'getListingsWithIssues' => ['getListingsWithIssues', 'GET', '/api/v2/account_health/get_listings_with_issues', []],
            'getLateOrders' => ['getLateOrders', 'GET', '/api/v2/account_health/get_late_orders', []],
            'getMetricSourceDetail' => ['getMetricSourceDetail', 'GET', '/api/v2/account_health/get_metric_source_detail', ['metric_id' => 1]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new AccountHealth($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
