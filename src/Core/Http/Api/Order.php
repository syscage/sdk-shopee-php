<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\Order\Booking;
use Syscage\Sdk\Shopee\Core\Http\Api\Order\Invoice;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Order API domain.
 *
 * Covers order lookup/lifecycle (cancel, split, notes, buyer cancellation,
 * prescription checks) and unshipped package/shipment queries.
 *
 * Bookings (a distinct pre-order/reservation entity, keyed by `booking_sn`
 * rather than `order_sn`) and invoices (local-seller tax documents, including
 * file upload/download) are distinct enough concepts to live behind their
 * own accessors — {@see booking()}, {@see invoice()}.
 *
 * @see .shopee-docs/API Reference/Order
 * @see .docs/api/Order
 */
final class Order
{
    private ?Booking $booking = null;
    private ?Invoice $invoice = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function booking(): Booking
    {
        return $this->booking ??= new Booking($this->client);
    }

    public function invoice(): Invoice
    {
        return $this->invoice ??= new Invoice($this->client);
    }

    // ------------------------------------------------------------------
    // Order Core
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/Order/get_order_list.md
     * @param array<string, mixed> $params time_range_field, time_from, time_to, page_size (required, max 15-day window); cursor, order_status, response_optional_fields (optional).
     * @return array<string, mixed>
     */
    public function getOrderList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/order/get_order_list', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Order/get_order_detail.md
     * @param array<string, mixed> $params order_sn_list (required, max 50); request_order_status_pending, response_optional_fields (optional).
     * @return array<string, mixed>
     */
    public function getOrderDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/order/get_order_detail', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Order/cancel_order.md
     * @param array<string, mixed> $params order_sn, cancel_reason (required); item_list, partial_cancel_item_list (optional, for partial cancellation).
     * @return array<string, mixed>
     */
    public function cancelOrder(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/order/cancel_order', $params);
    }

    /**
     * Max 30 parcels (TW) / 5 parcels (other regions) per split.
     *
     * @see .shopee-docs/API Reference/Order/split_order.md
     * @param array<string, mixed> $params order_sn, package_list (required).
     * @return array<string, mixed>
     */
    public function splitOrder(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/order/split_order', $params);
    }

    /**
     * Only valid while the order is still READY_TO_SHIP.
     *
     * @see .shopee-docs/API Reference/Order/unsplit_order.md
     * @param array<string, mixed> $params order_sn (required).
     * @return array<string, mixed>
     */
    public function unsplitOrder(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/order/unsplit_order', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Order/set_note.md
     * @param array<string, mixed> $params order_sn, note (required).
     * @return array<string, mixed>
     */
    public function setNote(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/order/set_note', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Order/handle_buyer_cancellation.md
     * @param array<string, mixed> $params order_sn, operation (required: "ACCEPT" or "REJECT").
     * @return array<string, mixed>
     */
    public function handleBuyerCancellation(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/order/handle_buyer_cancellation', $params);
    }

    /**
     * Preview the refund value of a partial cancellation before submitting it.
     *
     * @see .shopee-docs/API Reference/Order/get_estimate_cancel_value.md
     * @param array<string, mixed> $params order_sn, partial_cancel_item_list (required).
     * @return array<string, mixed>
     */
    public function getEstimateCancelValue(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/order/get_estimate_cancel_value', $params);
    }

    /**
     * Indonesia, Philippines, Thailand prescription orders only.
     *
     * @see .shopee-docs/API Reference/Order/handle_prescription_check.md
     * @param array<string, mixed> $params order_sn, is_approved (required); reject_reason_code, items, pharmacist_name, free_text (optional).
     * @return array<string, mixed>
     */
    public function handlePrescriptionCheck(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/order/handle_prescription_check', $params);
    }

    // ------------------------------------------------------------------
    // Package & Shipment
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/Order/get_package_detail.md
     * @param array<string, mixed> $params package_number_list (required, max 50).
     * @return array<string, mixed>
     */
    public function getPackageDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/order/get_package_detail', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Order/search_package_list.md
     * @param array<string, mixed> $params pagination (required); filter, sort (optional).
     * @return array<string, mixed>
     */
    public function searchPackageList(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/order/search_package_list', $params);
    }

    /**
     * Get orders in READY_TO_SHIP/RETRY_SHIP status, to begin shipping.
     *
     * @see .shopee-docs/API Reference/Order/get_shipment_list.md
     * @param array<string, mixed> $params page_size (required); cursor (optional).
     * @return array<string, mixed>
     */
    public function getShipmentList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/order/get_shipment_list', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Order/get_warehouse_filter_config.md
     * @return array<string, mixed>
     */
    public function getWarehouseFilterConfig(): array
    {
        return $this->client->shop('GET', '/api/v2/order/get_warehouse_filter_config');
    }
}
