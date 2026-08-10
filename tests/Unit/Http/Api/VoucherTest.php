<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Voucher;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class VoucherTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addVoucher' => ['addVoucher', 'POST', '/api/v2/voucher/add_voucher', ['voucher_name' => 'x', 'voucher_code' => 'TEST1', 'start_time' => 1, 'end_time' => 2, 'voucher_type' => 1, 'reward_type' => 1, 'usage_quantity' => 10, 'min_basket_price' => 1.0]],
            'updateVoucher' => ['updateVoucher', 'POST', '/api/v2/voucher/update_voucher', ['voucher_id' => 1]],
            'deleteVoucher' => ['deleteVoucher', 'POST', '/api/v2/voucher/delete_voucher', ['voucher_id' => 1]],
            'endVoucher' => ['endVoucher', 'POST', '/api/v2/voucher/end_voucher', ['voucher_id' => 1]],
            'getVoucher' => ['getVoucher', 'GET', '/api/v2/voucher/get_voucher', ['voucher_id' => 1]],
            'getVoucherList' => ['getVoucherList', 'GET', '/api/v2/voucher/get_voucher_list', ['status' => 'all']],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Voucher($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
