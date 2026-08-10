<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Order;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class OrderTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getOrderList' => ['getOrderList', 'GET', '/api/v2/order/get_order_list', ['time_range_field' => 'create_time', 'time_from' => 1, 'time_to' => 2, 'page_size' => 10]],
            'getOrderDetail' => ['getOrderDetail', 'GET', '/api/v2/order/get_order_detail', ['order_sn_list' => '1']],
            'cancelOrder' => ['cancelOrder', 'POST', '/api/v2/order/cancel_order', ['order_sn' => '1', 'cancel_reason' => 'OUT_OF_STOCK']],
            'splitOrder' => ['splitOrder', 'POST', '/api/v2/order/split_order', ['order_sn' => '1', 'package_list' => []]],
            'unsplitOrder' => ['unsplitOrder', 'POST', '/api/v2/order/unsplit_order', ['order_sn' => '1']],
            'setNote' => ['setNote', 'POST', '/api/v2/order/set_note', ['order_sn' => '1', 'note' => 'x']],
            'handleBuyerCancellation' => ['handleBuyerCancellation', 'POST', '/api/v2/order/handle_buyer_cancellation', ['order_sn' => '1', 'operation' => 'ACCEPT']],
            'getEstimateCancelValue' => ['getEstimateCancelValue', 'POST', '/api/v2/order/get_estimate_cancel_value', ['order_sn' => '1', 'partial_cancel_item_list' => []]],
            'handlePrescriptionCheck' => ['handlePrescriptionCheck', 'POST', '/api/v2/order/handle_prescription_check', ['order_sn' => '1', 'is_approved' => true]],

            'getPackageDetail' => ['getPackageDetail', 'GET', '/api/v2/order/get_package_detail', ['package_number_list' => '1']],
            'searchPackageList' => ['searchPackageList', 'POST', '/api/v2/order/search_package_list', ['pagination' => []]],
            'getShipmentList' => ['getShipmentList', 'GET', '/api/v2/order/get_shipment_list', ['page_size' => 10]],
            'getWarehouseFilterConfig' => ['getWarehouseFilterConfig', 'GET', '/api/v2/order/get_warehouse_filter_config', []],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Order($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testSubAccessorsAreLazilyCachedPerInstance(): void
    {
        $order = new Order($this->makeClient([]));

        $this->assertSame($order->booking(), $order->booking());
        $this->assertSame($order->invoice(), $order->invoice());
    }
}
