<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Logistics;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Logistics for bookings (see `Order\Booking` for the booking entity
 * itself) — arranging shipment, tracking, and shipping documents, keyed by
 * `booking_sn` instead of `order_sn`.
 *
 * Accessed via `$shopee->logistics()->booking()`.
 *
 * @see .shopee-docs/API Reference/Logistics (ship_booking, get_booking_tracking_*, *_booking_shipping_document*)
 * @see .docs/api/Logistics/Booking.md
 */
final class Booking
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/ship_booking.md
     * @param array<string, mixed> $params booking_sn (required); pickup, dropoff (optional).
     * @return array<string, mixed>
     */
    public function shipBooking(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/ship_booking', $params);
    }

    /**
     * May return empty until Shopee assigns one — poll roughly every 5 minutes.
     *
     * @see .shopee-docs/API Reference/Logistics/get_booking_tracking_number.md
     * @param array<string, mixed> $params booking_sn (required).
     * @return array<string, mixed>
     */
    public function getBookingTrackingNumber(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_booking_tracking_number', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/get_booking_tracking_info.md
     * @param array<string, mixed> $params booking_sn (required).
     * @return array<string, mixed>
     */
    public function getBookingTrackingInfo(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_booking_tracking_info', $params);
    }

    /**
     * Get info-needed/pickup options for a booking before shipping it.
     *
     * @see .shopee-docs/API Reference/Logistics/get_booking_shipping_parameter.md
     * @param array<string, mixed> $params booking_sn (required).
     * @return array<string, mixed>
     */
    public function getBookingShippingParameter(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_booking_shipping_parameter', $params);
    }

    /**
     * Create an async AWB generation task for bookings.
     * Poll {@see getBookingShippingDocumentResult()}.
     *
     * @see .shopee-docs/API Reference/Logistics/create_booking_shipping_document.md
     * @param array<string, mixed> $params booking_list (required): [{booking_sn, tracking_number, shipping_document_type}].
     * @return array<string, mixed>
     */
    public function createBookingShippingDocument(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/create_booking_shipping_document', $params);
    }

    /**
     * @return string Raw waybill file bytes.
     *
     * @see .shopee-docs/API Reference/Logistics/download_booking_shipping_document.md
     * @param array<string, mixed> $params booking_list (required); shipping_document_type (optional).
     */
    public function downloadBookingShippingDocument(array $params): string
    {
        return $this->client->shopDownloadPost('/api/v2/logistics/download_booking_shipping_document', $params);
    }

    /**
     * Raw logistics fields for a self-designed AWB. Optional rendered
     * address images are returned as base64 PNG strings within the JSON —
     * not a file download.
     *
     * @see .shopee-docs/API Reference/Logistics/get_booking_shipping_document_data_info.md
     * @param array<string, mixed> $params booking_sn (required); recipient_address_info (optional).
     * @return array<string, mixed>
     */
    public function getBookingShippingDocumentDataInfo(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/get_booking_shipping_document_data_info', $params);
    }

    /**
     * Get the selectable/suggested `shipping_document_type` for bookings.
     *
     * @see .shopee-docs/API Reference/Logistics/get_booking_shipping_document_parameter.md
     * @param array<string, mixed> $params booking_list (required).
     * @return array<string, mixed>
     */
    public function getBookingShippingDocumentParameter(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/get_booking_shipping_document_parameter', $params);
    }

    /**
     * Poll status of a task from {@see createBookingShippingDocument()}. Statuses: READY/FAILED/PROCESSING.
     *
     * @see .shopee-docs/API Reference/Logistics/get_booking_shipping_document_result.md
     * @param array<string, mixed> $params booking_list (required).
     * @return array<string, mixed>
     */
    public function getBookingShippingDocumentResult(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/get_booking_shipping_document_result', $params);
    }
}
