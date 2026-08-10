<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Fbs;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class FbsTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'queryBrShopEnrollmentStatus' => ['queryBrShopEnrollmentStatus', 'GET', '/api/v2/fbs/query_br_shop_enrollment_status', []],
            'queryBrShopBlockStatus' => ['queryBrShopBlockStatus', 'GET', '/api/v2/fbs/query_br_shop_block_status', []],
            'queryBrSkuBlockStatus' => ['queryBrSkuBlockStatus', 'GET', '/api/v2/fbs/query_br_sku_block_status', ['shop_sku_id' => '123_456']],
            'queryBrShopInvoiceError' => ['queryBrShopInvoiceError', 'GET', '/api/v2/fbs/query_br_shop_invoice_error', ['page_no' => 1, 'page_size' => 50]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Fbs($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());

        $query = $this->lastRequestQuery();
        $this->assertArrayHasKey('access_token', $query);
        $this->assertArrayHasKey('shop_id', $query);
    }
}
