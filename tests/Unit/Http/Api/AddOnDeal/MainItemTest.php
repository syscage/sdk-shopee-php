<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\AddOnDeal;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\AddOnDeal\MainItem;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class MainItemTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addAddOnDealMainItem' => ['addAddOnDealMainItem', 'POST', '/api/v2/add_on_deal/add_add_on_deal_main_item', ['add_on_deal_id' => 1, 'main_item_list' => [['item_id' => 1, 'status' => 1]]]],
            'updateAddOnDealMainItem' => ['updateAddOnDealMainItem', 'POST', '/api/v2/add_on_deal/update_add_on_deal_main_item', ['add_on_deal_id' => 1, 'main_item_list' => [['item_id' => 1, 'status' => 2]]]],
            'deleteAddOnDealMainItem' => ['deleteAddOnDealMainItem', 'POST', '/api/v2/add_on_deal/delete_add_on_deal_main_item', ['add_on_deal_id' => 1, 'main_item_list' => [1]]],
            'getAddOnDealMainItem' => ['getAddOnDealMainItem', 'GET', '/api/v2/add_on_deal/get_add_on_deal_main_item', ['add_on_deal_id' => 1]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new MainItem($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testDeleteMainItemUsesFlatIdList(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new MainItem($client))->deleteAddOnDealMainItem(['add_on_deal_id' => 1, 'main_item_list' => [1, 2, 3]]);

        $this->assertSame(
            ['add_on_deal_id' => 1, 'main_item_list' => [1, 2, 3]],
            $this->lastRequestBodyAsArray(),
        );
    }
}
