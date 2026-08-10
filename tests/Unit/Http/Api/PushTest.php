<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Push;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class PushTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getAppPushConfig' => ['getAppPushConfig', 'GET', '/api/v2/push/get_app_push_config', []],
            'setAppPushConfig' => ['setAppPushConfig', 'POST', '/api/v2/push/set_app_push_config', ['callback_url' => 'https://example.test/callback']],
            'getLostPushMessage' => ['getLostPushMessage', 'GET', '/api/v2/push/get_lost_push_message', []],
            'confirmConsumedLostPushMessage' => ['confirmConsumedLostPushMessage', 'POST', '/api/v2/push/confirm_consumed_lost_push_message', ['last_message_id' => 176610]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        (new Push($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    #[DataProvider('endpoints')]
    public function testEndpointSignsAtPublicLevel(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        (new Push($client))->{$method}($params);

        $query = $this->lastRequestQuery();
        $this->assertArrayHasKey('partner_id', $query);
        $this->assertArrayNotHasKey('access_token', $query);
        $this->assertArrayNotHasKey('shop_id', $query);
    }
}
