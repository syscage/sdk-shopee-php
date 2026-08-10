<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\AddOnDeal;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\AddOnDeal\SubItem;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class SubItemTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addAddOnDealSubItem' => ['addAddOnDealSubItem', 'POST', '/api/v2/add_on_deal/add_add_on_deal_sub_item', ['add_on_deal_id' => 1, 'sub_item_list' => [['item_id' => 1, 'sub_item_input_price' => 9.9]]]],
            'updateAddOnDealSubItem' => ['updateAddOnDealSubItem', 'POST', '/api/v2/add_on_deal/update_add_on_deal_sub_item', ['add_on_deal_id' => 1, 'sub_item_list' => [['item_id' => 1, 'sub_item_input_price' => 8.9]]]],
            'deleteAddOnDealSubItem' => ['deleteAddOnDealSubItem', 'POST', '/api/v2/add_on_deal/delete_add_on_deal_sub_item', ['add_on_deal_id' => 1, 'sub_item_list' => [['item_id' => 1, 'model_id' => 0]]]],
            'getAddOnDealSubItem' => ['getAddOnDealSubItem', 'GET', '/api/v2/add_on_deal/get_add_on_deal_sub_item', ['add_on_deal_id' => 1]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new SubItem($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testDeleteSubItemStaysObjectShapedWithModelId(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new SubItem($client))->deleteAddOnDealSubItem([
            'add_on_deal_id' => 1,
            'sub_item_list' => [['item_id' => 1, 'model_id' => 5]],
        ]);

        $this->assertSame(
            ['add_on_deal_id' => 1, 'sub_item_list' => [['item_id' => 1, 'model_id' => 5]]],
            $this->lastRequestBodyAsArray(),
        );
    }
}
