<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\ShopFlashSale;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\ShopFlashSale\Item;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ItemTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getItemCriteria' => ['getItemCriteria', 'GET', '/api/v2/shop_flash_sale/get_item_criteria', []],
            'addItems' => ['addItems', 'POST', '/api/v2/shop_flash_sale/add_shop_flash_sale_items', ['flash_sale_id' => 1, 'items' => [['item_id' => 1, 'purchase_limit' => 1]]]],
            'updateItems' => ['updateItems', 'POST', '/api/v2/shop_flash_sale/update_shop_flash_sale_items', ['flash_sale_id' => 1, 'items' => [['item_id' => 1]]]],
            'deleteItems' => ['deleteItems', 'POST', '/api/v2/shop_flash_sale/delete_shop_flash_sale_items', ['flash_sale_id' => 1, 'item_ids' => [1]]],
            'getItems' => ['getItems', 'GET', '/api/v2/shop_flash_sale/get_shop_flash_sale_items', ['flash_sale_id' => 1, 'offset' => 0, 'limit' => 100]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Item($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
