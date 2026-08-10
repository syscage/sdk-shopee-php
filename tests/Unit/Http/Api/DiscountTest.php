<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Discount;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class DiscountTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addDiscount' => ['addDiscount', 'POST', '/api/v2/discount/add_discount', ['discount_name' => 'x', 'start_time' => 1, 'end_time' => 2]],
            'updateDiscount' => ['updateDiscount', 'POST', '/api/v2/discount/update_discount', ['discount_id' => 1]],
            'deleteDiscount' => ['deleteDiscount', 'POST', '/api/v2/discount/delete_discount', ['discount_id' => 1]],
            'endDiscount' => ['endDiscount', 'POST', '/api/v2/discount/end_discount', ['discount_id' => 1]],
            'getDiscount' => ['getDiscount', 'GET', '/api/v2/discount/get_discount', ['discount_id' => 1, 'page_no' => 1, 'page_size' => 50]],
            'getDiscountList' => ['getDiscountList', 'GET', '/api/v2/discount/get_discount_list', ['discount_status' => 'ongoing', 'page_no' => 1, 'page_size' => 100]],

            'addDiscountItem' => ['addDiscountItem', 'POST', '/api/v2/discount/add_discount_item', ['discount_id' => 1, 'item_list' => []]],
            'updateDiscountItem' => ['updateDiscountItem', 'POST', '/api/v2/discount/update_discount_item', ['discount_id' => 1, 'item_list' => []]],
            'deleteDiscountItem' => ['deleteDiscountItem', 'POST', '/api/v2/discount/delete_discount_item', ['discount_id' => 1, 'item_id' => 1]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Discount($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testSipIsLazilyCachedPerInstance(): void
    {
        $discount = new Discount($this->makeClient([]));

        $this->assertSame($discount->sip(), $discount->sip());
    }
}
