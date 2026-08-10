<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Merchant;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class MerchantTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getMerchantInfo' => ['getMerchantInfo', 'GET', '/api/v2/merchant/get_merchant_info', []],
            'getMerchantPrepaidAccountList' => ['getMerchantPrepaidAccountList', 'GET', '/api/v2/merchant/get_merchant_prepaid_account_list', ['page_no' => 1, 'page_size' => 10]],
            'getMerchantWarehouseList' => ['getMerchantWarehouseList', 'POST', '/api/v2/merchant/get_merchant_warehouse_list', ['cursor' => ['next_id' => 0, 'page_size' => 30], 'warehouse_type' => 1]],
            'getMerchantWarehouseLocationList' => ['getMerchantWarehouseLocationList', 'GET', '/api/v2/merchant/get_merchant_warehouse_location_list', []],
            'getShopListByMerchant' => ['getShopListByMerchant', 'GET', '/api/v2/merchant/get_shop_list_by_merchant', ['page_no' => 1, 'page_size' => 100]],
            // Path from Shopee's own code examples, not its doc header — see docblock.
            'getWarehouseEligibleShopList' => ['getWarehouseEligibleShopList', 'POST', '/api/v2/merchant/list_shop_by_warehouse', ['warehouse_id' => 1, 'warehouse_type' => 1, 'cursor' => ['next_id' => 0, 'page_size' => 4]]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, merchantId: 1001705));

        (new Merchant($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testCallsSignAsMerchantLevelNotShopLevel(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, merchantId: 1001705));

        (new Merchant($client))->getMerchantInfo();

        $query = $this->lastRequestQuery();
        $this->assertSame('1001705', $query['merchant_id']);
        $this->assertSame('token-123', $query['access_token']);
        $this->assertArrayNotHasKey('shop_id', $query);
    }
}
