<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Logistics;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class LogisticsTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getShippingParameter' => ['getShippingParameter', 'GET', '/api/v2/logistics/get_shipping_parameter', ['order_sn' => '1']],
            'getChannelList' => ['getChannelList', 'GET', '/api/v2/logistics/get_channel_list', []],
            'updateChannel' => ['updateChannel', 'POST', '/api/v2/logistics/update_channel', ['logistics_channel_id' => 1]],
            'getMassShippingParameter' => ['getMassShippingParameter', 'POST', '/api/v2/logistics/get_mass_shipping_parameter', ['package_list' => []]],

            'shipOrder' => ['shipOrder', 'POST', '/api/v2/logistics/ship_order', ['order_sn' => '1']],
            'batchShipOrder' => ['batchShipOrder', 'POST', '/api/v2/logistics/batch_ship_order', ['order_list' => []]],
            'massShipOrder' => ['massShipOrder', 'POST', '/api/v2/logistics/mass_ship_order', ['package_list' => []]],
            'updateShippingOrder' => ['updateShippingOrder', 'POST', '/api/v2/logistics/update_shipping_order', ['order_sn' => '1', 'pickup' => []]],
            'updateSelfCollectionOrderLogistics' => ['updateSelfCollectionOrderLogistics', 'POST', '/api/v2/logistics/update_self_collection_order_logistics', ['package_number' => '1', 'self_collection_logistics_action' => 'READY']],
            'updateTrackingStatus' => ['updateTrackingStatus', 'POST', '/api/v2/logistics/update_tracking_status', ['order_sn' => '1', 'logistics_status' => 'PICKED_UP']],
            'batchUpdateTpfWarehouseTrackingStatus' => ['batchUpdateTpfWarehouseTrackingStatus', 'POST', '/api/v2/logistics/batch_update_tpf_warehouse_tracking_status', ['tpf_name' => 'x', 'tpf_tracking_status' => 'IN', 'package_list' => []]],

            'getTrackingNumber' => ['getTrackingNumber', 'GET', '/api/v2/logistics/get_tracking_number', ['order_sn' => '1']],
            'getTrackingInfo' => ['getTrackingInfo', 'GET', '/api/v2/logistics/get_tracking_info', ['order_sn' => '1']],
            'getMassTrackingNumber' => ['getMassTrackingNumber', 'POST', '/api/v2/logistics/get_mass_tracking_number', ['package_list' => []]],

            'getPauseStatus' => ['getPauseStatus', 'GET', '/api/v2/logistics/get_pause_status', []],
            'setPauseStatus' => ['setPauseStatus', 'POST', '/api/v2/logistics/set_pause_status', ['is_paused' => true]],

            'getMartPackagingInfo' => ['getMartPackagingInfo', 'GET', '/api/v2/logistics/get_mart_packaging_info', []],
            'setMartPackagingInfo' => ['setMartPackagingInfo', 'POST', '/api/v2/logistics/set_mart_packaging_info', ['enable' => false]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Logistics($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testSubAccessorsAreLazilyCachedPerInstance(): void
    {
        $logistics = new Logistics($this->makeClient([]));

        $this->assertSame($logistics->address(), $logistics->address());
        $this->assertSame($logistics->booking(), $logistics->booking());
        $this->assertSame($logistics->operatingHours(), $logistics->operatingHours());
        $this->assertSame($logistics->serviceableArea(), $logistics->serviceableArea());
        $this->assertSame($logistics->shippingDocument(), $logistics->shippingDocument());
    }
}
