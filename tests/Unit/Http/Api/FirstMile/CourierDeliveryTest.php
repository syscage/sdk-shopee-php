<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\FirstMile;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\FirstMile\CourierDelivery;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class CourierDeliveryTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getChannelList' => ['getChannelList', 'GET', '/api/v2/first_mile/get_courier_delivery_channel_list', []],
            'generateAndBindTrackingNumber' => ['generateAndBindTrackingNumber', 'POST', '/api/v2/first_mile/generate_and_bind_first_mile_tracking_number', ['shipment_method' => 'courier_delivery', 'order_list' => [['order_sn' => 'O1']], 'courier_delivery_info' => ['address_id' => 1, 'warehouse_id' => 'W1', 'logistics_product_id' => 1, 'courier_service_id' => 'C1']]],
            'bindTrackingNumber' => ['bindTrackingNumber', 'POST', '/api/v2/first_mile/bind_courier_delivery_first_mile_tracking_number', ['shipment_method' => 'courier_delivery', 'binding_id' => 'B1', 'order_list' => [['order_sn' => 'O1']]]],
            'getTrackingNumberList' => ['getTrackingNumberList', 'POST', '/api/v2/first_mile/get_courier_delivery_tracking_number_list', ['from_date' => '2026-01-01', 'to_date' => '2026-01-31']],
            'getDetail' => ['getDetail', 'GET', '/api/v2/first_mile/get_courier_delivery_detail', ['binding_id' => 'B1']],
            'getWaybill' => ['getWaybill', 'POST', '/api/v2/first_mile/get_courier_delivery_waybill', ['binding_id_list' => ['B1']]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new CourierDelivery($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
