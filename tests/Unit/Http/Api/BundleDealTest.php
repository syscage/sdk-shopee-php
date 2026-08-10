<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\BundleDeal;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class BundleDealTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addBundleDeal' => ['addBundleDeal', 'POST', '/api/v2/bundle_deal/add_bundle_deal', ['rule_type' => 1, 'min_amount' => 2, 'start_time' => 1, 'end_time' => 2, 'name' => 'x', 'purchase_limit' => 0]],
            'updateBundleDeal' => ['updateBundleDeal', 'POST', '/api/v2/bundle_deal/update_bundle_deal', ['bundle_deal_id' => 1]],
            'deleteBundleDeal' => ['deleteBundleDeal', 'POST', '/api/v2/bundle_deal/delete_bundle_deal', ['bundle_deal_id' => 1]],
            'endBundleDeal' => ['endBundleDeal', 'POST', '/api/v2/bundle_deal/end_bundle_deal', ['bundle_deal_id' => 1]],
            'getBundleDeal' => ['getBundleDeal', 'GET', '/api/v2/bundle_deal/get_bundle_deal', ['bundle_deal_id' => 1]],
            'getBundleDealList' => ['getBundleDealList', 'GET', '/api/v2/bundle_deal/get_bundle_deal_list', []],

            'addBundleDealItem' => ['addBundleDealItem', 'POST', '/api/v2/bundle_deal/add_bundle_deal_item', ['bundle_deal_id' => 1, 'item_list' => []]],
            'updateBundleDealItem' => ['updateBundleDealItem', 'POST', '/api/v2/bundle_deal/update_bundle_deal_item', ['bundle_deal_id' => 1, 'item_list' => []]],
            'deleteBundleDealItem' => ['deleteBundleDealItem', 'POST', '/api/v2/bundle_deal/delete_bundle_deal_item', ['bundle_deal_id' => 1, 'item_list' => []]],
            'getBundleDealItem' => ['getBundleDealItem', 'GET', '/api/v2/bundle_deal/get_bundle_deal_item', ['bundle_deal_id' => 1]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new BundleDeal($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
