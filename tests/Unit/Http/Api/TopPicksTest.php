<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\TopPicks;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class TopPicksTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addTopPicks' => ['addTopPicks', 'POST', '/api/v2/top_picks/add_top_picks', ['name' => 'test', 'item_id_list' => [1, 2, 3], 'is_activated' => true]],
            'updateTopPicks' => ['updateTopPicks', 'POST', '/api/v2/top_picks/update_top_picks', ['top_picks_id' => 480]],
            'deleteTopPicks' => ['deleteTopPicks', 'POST', '/api/v2/top_picks/delete_top_picks', ['top_picks_id' => 480]],
            'getTopPicksList' => ['getTopPicksList', 'GET', '/api/v2/top_picks/get_top_picks_list', []],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new TopPicks($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
