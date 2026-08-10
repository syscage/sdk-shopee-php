<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\PublicApi;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class PublicApiTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getTokenByResendCode' => ['getTokenByResendCode', 'POST', '/api/v2/public/get_token_by_resend_code', ['resend_code' => 'resend123']],
            'getShopeeIpRanges' => ['getShopeeIpRanges', 'GET', '/api/v2/public/get_shopee_ip_ranges', []],
            'getMerchantsByPartner' => ['getMerchantsByPartner', 'GET', '/api/v2/public/get_merchants_by_partner', []],
            'getShopsByPartner' => ['getShopsByPartner', 'GET', '/api/v2/public/get_shops_by_partner', []],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        (new PublicApi($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    #[DataProvider('endpoints')]
    public function testEndpointSignsAtPublicLevel(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        (new PublicApi($client))->{$method}($params);

        $query = $this->lastRequestQuery();
        $this->assertArrayHasKey('partner_id', $query);
        $this->assertArrayNotHasKey('access_token', $query);
        $this->assertArrayNotHasKey('shop_id', $query);
    }
}
