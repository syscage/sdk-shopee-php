<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\AddOnDeal;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class AddOnDealTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'addAddOnDeal' => ['addAddOnDeal', 'POST', '/api/v2/add_on_deal/add_add_on_deal', ['add_on_deal_name' => 'x', 'start_time' => 1, 'end_time' => 2, 'promotion_type' => 0]],
            'updateAddOnDeal' => ['updateAddOnDeal', 'POST', '/api/v2/add_on_deal/update_add_on_deal', ['add_on_deal_id' => 1]],
            'deleteAddOnDeal' => ['deleteAddOnDeal', 'POST', '/api/v2/add_on_deal/delete_add_on_deal', ['add_on_deal_id' => 1]],
            'endAddOnDeal' => ['endAddOnDeal', 'POST', '/api/v2/add_on_deal/end_add_on_deal', ['add_on_deal_id' => 1]],
            'getAddOnDeal' => ['getAddOnDeal', 'GET', '/api/v2/add_on_deal/get_add_on_deal', ['add_on_deal_id' => 1]],
            'getAddOnDealList' => ['getAddOnDealList', 'GET', '/api/v2/add_on_deal/get_add_on_deal_list', ['promotion_status' => 'ongoing']],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new AddOnDeal($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testSubAccessorsAreLazilyCachedPerInstance(): void
    {
        $addOnDeal = new AddOnDeal($this->makeClient([]));

        $this->assertSame($addOnDeal->mainItem(), $addOnDeal->mainItem());
        $this->assertSame($addOnDeal->subItem(), $addOnDeal->subItem());
    }
}
