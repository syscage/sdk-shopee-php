<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\ShopCategory;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ShopCategoryTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addShopCategory' => ['addShopCategory', 'POST', '/api/v2/shop_category/add_shop_category', ['name' => 'Test']],
            'updateShopCategory' => ['updateShopCategory', 'POST', '/api/v2/shop_category/update_shop_category', ['shop_category_id' => 1]],
            'deleteShopCategory' => ['deleteShopCategory', 'POST', '/api/v2/shop_category/delete_shop_category', ['shop_category_id' => 1]],
            'getShopCategoryList' => ['getShopCategoryList', 'GET', '/api/v2/shop_category/get_shop_category_list', ['page_size' => 100, 'page_no' => 1]],
            'addItemList' => ['addItemList', 'POST', '/api/v2/shop_category/add_item_list', ['shop_category_id' => 1, 'item_list' => [1, 2]]],
            'deleteItemList' => ['deleteItemList', 'POST', '/api/v2/shop_category/delete_item_list', ['shop_category_id' => 1, 'item_list' => [1, 2]]],
            'getItemList' => ['getItemList', 'GET', '/api/v2/shop_category/get_item_list', ['shop_category_id' => 1]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new ShopCategory($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
