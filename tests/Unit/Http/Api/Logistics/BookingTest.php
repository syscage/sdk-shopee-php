<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Logistics;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Logistics\Booking;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class BookingTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'shipBooking' => ['shipBooking', 'POST', '/api/v2/logistics/ship_booking', ['booking_sn' => '1']],
            'getBookingTrackingNumber' => ['getBookingTrackingNumber', 'GET', '/api/v2/logistics/get_booking_tracking_number', ['booking_sn' => '1']],
            'getBookingTrackingInfo' => ['getBookingTrackingInfo', 'GET', '/api/v2/logistics/get_booking_tracking_info', ['booking_sn' => '1']],
            'getBookingShippingParameter' => ['getBookingShippingParameter', 'GET', '/api/v2/logistics/get_booking_shipping_parameter', ['booking_sn' => '1']],
            'createBookingShippingDocument' => ['createBookingShippingDocument', 'POST', '/api/v2/logistics/create_booking_shipping_document', ['booking_list' => []]],
            'downloadBookingShippingDocument' => ['downloadBookingShippingDocument', 'POST', '/api/v2/logistics/download_booking_shipping_document', ['booking_list' => []]],
            'getBookingShippingDocumentDataInfo' => ['getBookingShippingDocumentDataInfo', 'POST', '/api/v2/logistics/get_booking_shipping_document_data_info', ['booking_sn' => '1']],
            'getBookingShippingDocumentParameter' => ['getBookingShippingDocumentParameter', 'POST', '/api/v2/logistics/get_booking_shipping_document_parameter', ['booking_list' => []]],
            'getBookingShippingDocumentResult' => ['getBookingShippingDocumentResult', 'POST', '/api/v2/logistics/get_booking_shipping_document_result', ['booking_list' => []]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Booking($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }
}
