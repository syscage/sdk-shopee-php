<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Product;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Product\Outlet;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class OutletTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'batchPublishItemToOutletShop' => ['batchPublishItemToOutletShop', 'POST', '/api/v2/product/batch_publish_item_to_outlet_shop', ['item_list' => []]],
            'publishItemToOutletShop' => ['publishItemToOutletShop', 'POST', '/api/v2/product/publish_item_to_outlet_shop', ['mart_item_id' => 1, 'outlet_shop_id' => 2]],
            'batchUpdateOutletPrice' => ['batchUpdateOutletPrice', 'POST', '/api/v2/product/batch_update_outlet_price', ['item_list' => []]],
            'batchUpdateOutletStock' => ['batchUpdateOutletStock', 'POST', '/api/v2/product/batch_update_outlet_stock', ['item_list' => []]],
            'getMartItemByOutletItemId' => ['getMartItemByOutletItemId', 'POST', '/api/v2/product/get_mart_item_by_outlet_item_id', ['outlet_item_id' => 1]],
            'getMartItemMappingById' => ['getMartItemMappingById', 'POST', '/api/v2/product/get_mart_item_mapping_by_id', ['mart_item_id' => 1, 'outlet_shop_id_list' => [1]]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Outlet($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
