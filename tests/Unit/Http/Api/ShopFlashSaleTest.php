<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\ShopFlashSale;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ShopFlashSaleTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getTimeSlotId' => ['getTimeSlotId', 'GET', '/api/v2/shop_flash_sale/get_time_slot_id', ['start_time' => 1, 'end_time' => 2]],
            'createShopFlashSale' => ['createShopFlashSale', 'POST', '/api/v2/shop_flash_sale/create_shop_flash_sale', ['timeslot_id' => 100]],
            'updateShopFlashSale' => ['updateShopFlashSale', 'POST', '/api/v2/shop_flash_sale/update_shop_flash_sale', ['flash_sale_id' => 1, 'status' => 1]],
            'deleteShopFlashSale' => ['deleteShopFlashSale', 'POST', '/api/v2/shop_flash_sale/delete_shop_flash_sale', ['flash_sale_id' => 1]],
            'getShopFlashSale' => ['getShopFlashSale', 'GET', '/api/v2/shop_flash_sale/get_shop_flash_sale', ['flash_sale_id' => 1]],
            'getShopFlashSaleList' => ['getShopFlashSaleList', 'GET', '/api/v2/shop_flash_sale/get_shop_flash_sale_list', ['type' => 0, 'offset' => 0, 'limit' => 100]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new ShopFlashSale($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testItemAccessorIsCached(): void
    {
        $shopFlashSale = new ShopFlashSale($this->makeClient([]));

        $this->assertSame($shopFlashSale->item(), $shopFlashSale->item());
    }
}
