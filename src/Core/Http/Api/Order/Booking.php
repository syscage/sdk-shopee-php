<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Order;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Bookings: an "advance fulfilment" reservation entity, keyed by
 * `booking_sn` rather than `order_sn`. A booking moves through
 * READY_TO_SHIP / PROCESSED / SHIPPED / CANCELLED, and links to a real
 * order (`order_sn`) once MATCHED.
 *
 * Accessed via `$shopee->order()->booking()`.
 *
 * @see .shopee-docs/API Reference/Order/get_booking_list.md
 * @see .shopee-docs/API Reference/Order/get_booking_detail.md
 * @see .docs/api/Order/Booking.md
 */
final class Booking
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Order/get_booking_list.md
     * @param array<string, mixed> $params time_range_field, time_from, time_to, page_size (required); cursor, booking_status (optional).
     * @return array<string, mixed>
     */
    public function getBookingList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/order/get_booking_list', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Order/get_booking_detail.md
     * @param array<string, mixed> $params booking_sn_list (required, max 50); response_optional_fields (optional).
     * @return array<string, mixed>
     */
    public function getBookingDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/order/get_booking_detail', $params);
    }
}
