<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\GlobalProduct;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\GlobalProduct\Publishing;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class PublishingTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'createPublishTask' => ['createPublishTask', 'POST', '/api/v2/global_product/create_publish_task', ['global_item_id' => 1, 'shop_id' => 2, 'shop_region' => 'SG']],
            'getPublishTaskResult' => ['getPublishTaskResult', 'GET', '/api/v2/global_product/get_publish_task_result', ['publish_task_id' => 'task-1']],
            'getPublishableShop' => ['getPublishableShop', 'GET', '/api/v2/global_product/get_publishable_shop', ['global_item_id' => 1]],
            'getPublishedList' => ['getPublishedList', 'GET', '/api/v2/global_product/get_published_list', ['global_item_id' => 1]],
            'getShopPublishableStatus' => ['getShopPublishableStatus', 'GET', '/api/v2/global_product/get_shop_publishable_status', ['global_item_id' => 1, 'offset' => 0, 'page_size' => 10]],
            'setSyncField' => ['setSyncField', 'POST', '/api/v2/global_product/set_sync_field', ['shop_sync_list' => []]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, merchantId: 1001705));

        (new Publishing($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
