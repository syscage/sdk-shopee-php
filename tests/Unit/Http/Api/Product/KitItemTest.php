<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Product;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Product\KitItem;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class KitItemTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addKitItem' => ['addKitItem', 'POST', '/api/v2/product/add_kit_item', ['item_setting' => []]],
            'updateKitItem' => ['updateKitItem', 'POST', '/api/v2/product/update_kit_item', ['item_id' => 1]],
            'getKitItemInfo' => ['getKitItemInfo', 'GET', '/api/v2/product/get_kit_item_info', ['item_id' => 1]],
            'getKitItemLimit' => ['getKitItemLimit', 'GET', '/api/v2/product/get_kit_item_limit', []],
            'generateKitImage' => ['generateKitImage', 'POST', '/api/v2/product/generate_kit_image', ['component_list' => []]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new KitItem($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
