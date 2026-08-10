<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\FollowPrize;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class FollowPrizeTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addFollowPrize' => ['addFollowPrize', 'POST', '/api/v2/follow_prize/add_follow_prize', ['follow_prize_name' => 'x', 'start_time' => 1, 'end_time' => 2, 'usage_quantity' => 10, 'min_spend' => 1.0, 'reward_type' => 1]],
            'updateFollowPrize' => ['updateFollowPrize', 'POST', '/api/v2/follow_prize/update_follow_prize', ['campaign_id' => 1]],
            'deleteFollowPrize' => ['deleteFollowPrize', 'POST', '/api/v2/follow_prize/delete_follow_prize', ['campaign_id' => 1]],
            'endFollowPrize' => ['endFollowPrize', 'POST', '/api/v2/follow_prize/end_follow_prize', ['campaign_id' => 1]],
            'getFollowPrizeDetail' => ['getFollowPrizeDetail', 'GET', '/api/v2/follow_prize/get_follow_prize_detail', []],
            'getFollowPrizeList' => ['getFollowPrizeList', 'GET', '/api/v2/follow_prize/get_follow_prize_list', ['status' => 'all']],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new FollowPrize($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
