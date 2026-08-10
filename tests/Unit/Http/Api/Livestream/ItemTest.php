<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Livestream;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Livestream\Item;
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
            'addItemList' => ['addItemList', 'POST', '/api/v2/livestream/add_item_list', ['session_id' => 1, 'item_list' => [['item_id' => 1, 'shop_id' => 14701711]]]],
            'updateItemList' => ['updateItemList', 'POST', '/api/v2/livestream/update_item_list', ['session_id' => 1, 'item_list' => [['item_id' => 1, 'shop_id' => 14701711]]]],
            'deleteItemList' => ['deleteItemList', 'POST', '/api/v2/livestream/delete_item_list', ['session_id' => 1, 'item_list' => [['item_id' => 1, 'shop_id' => 14701711]]]],
            'getItemList' => ['getItemList', 'GET', '/api/v2/livestream/get_item_list', ['session_id' => 1, 'offset' => 0, 'page_size' => 20]],
            'getItemCount' => ['getItemCount', 'GET', '/api/v2/livestream/get_item_count', ['session_id' => 1]],
            'getRecentItemList' => ['getRecentItemList', 'GET', '/api/v2/livestream/get_recent_item_list', ['offset' => 0, 'page_size' => 20]],
            'getLikeItemList' => ['getLikeItemList', 'GET', '/api/v2/livestream/get_like_item_list', ['offset' => 0, 'page_size' => 20]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, userId: 987654));

        (new Item($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
