<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Shop;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ShopTest extends TestCase
{
    use InteractsWithMockHttp;

    public function testGetShopInfoCallsExpectedEndpoint(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'error' => '',
                'message' => '',
                'shop_name' => 'mysipuat',
                'region' => 'MY',
                'status' => 'NORMAL',
            ])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        $response = (new Shop($client))->getShopInfo();

        $this->assertSame('mysipuat', $response['shop_name']);
        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/shop/get_shop_info', $this->lastRequest()->getUri()->getPath());
    }

    public function testUpdateProfileSendsParamsInBody(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => '', 'response' => []])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Shop($client))->updateProfile(['shop_name' => 'New Name']);

        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/shop/update_profile', $this->lastRequest()->getUri()->getPath());
        $this->assertSame(['shop_name' => 'New Name'], $this->lastRequestBodyAsArray());
    }
}
