<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\FirstMile;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Shopee-brokered 3PL courier pickup, a distinct first-mile workflow from
 * the parent {@see \Syscage\Sdk\Shopee\Core\Http\Api\FirstMile} class:
 * `shipment_method = courier_delivery` only, keyed by `binding_id` rather
 * than a `first_mile_tracking_number`. There's no separate
 * generate/bind pair here — {@see generateAndBindTrackingNumber()} does
 * both in one call (the plain family's generate/bind split explicitly
 * excludes `courier_delivery` as a shipment method), producing the
 * `binding_id` that {@see bindTrackingNumber()} can then add more orders
 * to.
 *
 * Unlike the parent class's {@see \Syscage\Sdk\Shopee\Core\Http\Api\FirstMile::getWaybill()}
 * (a raw file download), {@see getWaybill()} here returns ordinary JSON
 * containing a `shipping_label_url` string to fetch separately.
 *
 * @see .shopee-docs/API Reference/FirstMile
 * @see .docs/api/FirstMile/CourierDelivery
 */
final class CourierDelivery
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params region (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/get_courier_delivery_channel_list.md
     */
    public function getChannelList(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/first_mile/get_courier_delivery_channel_list', $params);
    }

    /**
     * Books the courier pickup and performs the initial order binding in
     * one call, returning a `binding_id`.
     *
     * @param array<string, mixed> $params shipment_method (required, must
     *     be `courier_delivery`), order_list, courier_delivery_info (both
     *     required); region (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/generate_and_bind_first_mile_tracking_number.md
     */
    public function generateAndBindTrackingNumber(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/first_mile/generate_and_bind_first_mile_tracking_number', $params);
    }

    /**
     * Add more orders to an existing `binding_id` from
     * {@see generateAndBindTrackingNumber()}.
     *
     * @param array<string, mixed> $params shipment_method, binding_id,
     *     order_list (all required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/bind_courier_delivery_first_mile_tracking_number.md
     */
    public function bindTrackingNumber(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/first_mile/bind_courier_delivery_first_mile_tracking_number', $params);
    }

    /**
     * @param array<string, mixed> $params from_date, to_date (both
     *     required); page_size, cursor (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/get_courier_delivery_tracking_number_list.md
     */
    public function getTrackingNumberList(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/first_mile/get_courier_delivery_tracking_number_list', $params);
    }

    /**
     * @param array<string, mixed> $params binding_id (required); cursor,
     *     page_size (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/get_courier_delivery_detail.md
     */
    public function getDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/first_mile/get_courier_delivery_detail', $params);
    }

    /**
     * Unlike the parent class's file-download `getWaybill()`, this returns
     * ordinary JSON with a `shipping_label_url` per binding to fetch
     * separately (not a Shopee-signed download).
     *
     * @param array<string, mixed> $params binding_id_list (required, max 50).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/get_courier_delivery_waybill.md
     */
    public function getWaybill(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/first_mile/get_courier_delivery_waybill', $params);
    }
}
