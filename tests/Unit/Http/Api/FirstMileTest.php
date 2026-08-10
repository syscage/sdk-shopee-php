<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\FirstMile;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class FirstMileTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getChannelList' => ['getChannelList', 'GET', '/api/v2/first_mile/get_channel_list', []],
            'getTransitWarehouseList' => ['getTransitWarehouseList', 'GET', '/api/v2/first_mile/get_transit_warehouse_list', []],
            'generateFirstMileTrackingNumber' => ['generateFirstMileTrackingNumber', 'POST', '/api/v2/first_mile/generate_first_mile_tracking_number', ['declare_date' => '2026-01-01']],
            'bindFirstMileTrackingNumber' => ['bindFirstMileTrackingNumber', 'POST', '/api/v2/first_mile/bind_first_mile_tracking_number', ['first_mile_tracking_number' => 'FM1', 'shipment_method' => 'pickup', 'region' => 'SG', 'logistics_channel_id' => 1, 'order_list' => [['order_sn' => 'O1']]]],
            'unbindFirstMileTrackingNumber' => ['unbindFirstMileTrackingNumber', 'POST', '/api/v2/first_mile/unbind_first_mile_tracking_number', ['first_mile_tracking_number' => 'FM1', 'order_list' => [['order_sn' => 'O1']]]],
            'unbindFirstMileTrackingNumberAll' => ['unbindFirstMileTrackingNumberAll', 'POST', '/api/v2/first_mile/unbind_first_mile_tracking_number_all', ['order_list' => [['order_sn' => 'O1']]]],
            'getTrackingNumberList' => ['getTrackingNumberList', 'GET', '/api/v2/first_mile/get_tracking_number_list', ['from_date' => '2026-01-01', 'to_date' => '2026-01-31']],
            'getUnbindOrderList' => ['getUnbindOrderList', 'GET', '/api/v2/first_mile/get_unbind_order_list', []],
            'getDetail' => ['getDetail', 'GET', '/api/v2/first_mile/get_detail', ['first_mile_tracking_number' => 'FM1']],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new FirstMile($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testGetWaybillReturnsRawBodyOnSuccess(): void
    {
        $client = $this->makeClient([
            new Response(200, ['Content-Type' => 'application/pdf'], '%PDF-1.4 raw bytes'),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        $bytes = (new FirstMile($client))->getWaybill(['first_mile_tracking_number_list' => ['FM1']]);

        $this->assertSame('%PDF-1.4 raw bytes', $bytes);
        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/first_mile/get_waybill', $this->lastRequest()->getUri()->getPath());
    }

    public function testCourierDeliveryAccessorIsCached(): void
    {
        $firstMile = new FirstMile($this->makeClient([]));

        $this->assertSame($firstMile->courierDelivery(), $firstMile->courierDelivery());
    }
}
