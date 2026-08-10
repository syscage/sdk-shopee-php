<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Payment;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Payment\Installment;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class InstallmentTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getItemInstallmentStatus' => ['getItemInstallmentStatus', 'POST', '/api/v2/payment/get_item_installment_status', ['item_id_list' => [1]]],
            'setItemInstallmentStatus' => ['setItemInstallmentStatus', 'POST', '/api/v2/payment/set_item_installment_status', ['item_id_list' => [1], 'tenure_list' => [3]]],
            'getShopInstallmentStatus' => ['getShopInstallmentStatus', 'GET', '/api/v2/payment/get_shop_installment_status', []],
            'setShopInstallmentStatus' => ['setShopInstallmentStatus', 'POST', '/api/v2/payment/set_shop_installment_status', ['installment_status' => 1]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Installment($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
