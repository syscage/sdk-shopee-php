<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\FirstMile\CourierDelivery;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * FirstMile API domain (`v2.first_mile.*`) — seller-arranged first-mile
 * logistics: the seller drops off, self-delivers, or arranges pickup of
 * orders at a channel/warehouse, rather than using Shopee's own courier
 * pickup. Covers `shipment_method` values `pickup`, `dropoff`, and
 * `self_deliver`, keyed by a Shopee-issued `first_mile_tracking_number`.
 *
 * A distinct, Shopee-brokered 3PL pickup workflow (`shipment_method =
 * courier_delivery`, keyed by `binding_id` instead) is split into
 * {@see CourierDelivery} (via {@see courierDelivery()}) — the identifiers,
 * request shape (address/courier/prepaid-account vs. channel/warehouse),
 * and even the waybill response shape (a downloadable file here vs. a JSON
 * `shipping_label_url` there) genuinely diverge between the two.
 * {@see unbindFirstMileTrackingNumberAll()} is the one endpoint shared by
 * both families (it unbinds by `order_sn` alone, needing no tracking
 * number or binding id).
 *
 * Typical flow: {@see getChannelList()} (+ {@see getTransitWarehouseList()}
 * for dropoff) → {@see generateFirstMileTrackingNumber()} (pickup/
 * self_deliver only — dropoff codes come from the channel supplier) →
 * {@see bindFirstMileTrackingNumber()} → {@see getTrackingNumberList()} /
 * {@see getDetail()} to monitor → {@see getWaybill()} to download the
 * label.
 *
 * Every endpoint signs at the **Shop** level.
 *
 * @see .shopee-docs/API Reference/FirstMile
 * @see .docs/api/FirstMile
 */
final class FirstMile
{
    private ?CourierDelivery $courierDelivery = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function courierDelivery(): CourierDelivery
    {
        return $this->courierDelivery ??= new CourierDelivery($this->client);
    }

    /**
     * @param array<string, mixed> $params region (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/get_channel_list.md
     */
    public function getChannelList(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/first_mile/get_channel_list', $params);
    }

    /**
     * @param array<string, mixed> $params region, shipment_method (both optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/get_transit_warehouse_list.md
     */
    public function getTransitWarehouseList(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/first_mile/get_transit_warehouse_list', $params);
    }

    /**
     * Only for `pickup`/`self_deliver` — dropoff tracking numbers are
     * supplied by the channel, not generated here.
     *
     * @param array<string, mixed> $params declare_date (required); quantity (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/generate_first_mile_tracking_number.md
     */
    public function generateFirstMileTrackingNumber(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/first_mile/generate_first_mile_tracking_number', $params);
    }

    /**
     * @param array<string, mixed> $params first_mile_tracking_number,
     *     shipment_method, region, logistics_channel_id, order_list (all
     *     required); volume, weight, width, length, height, warehouse_id,
     *     warehouse_type (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/bind_first_mile_tracking_number.md
     */
    public function bindFirstMileTrackingNumber(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/first_mile/bind_first_mile_tracking_number', $params);
    }

    /**
     * @param array<string, mixed> $params first_mile_tracking_number,
     *     order_list (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/unbind_first_mile_tracking_number.md
     */
    public function unbindFirstMileTrackingNumber(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/first_mile/unbind_first_mile_tracking_number', $params);
    }

    /**
     * Unbinds by `order_sn` alone — works regardless of whether the order
     * was bound under this class or {@see CourierDelivery}.
     *
     * @param array<string, mixed> $params order_list (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/unbind_first_mile_tracking_number_all.md
     */
    public function unbindFirstMileTrackingNumberAll(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/first_mile/unbind_first_mile_tracking_number_all', $params);
    }

    /**
     * @param array<string, mixed> $params from_date, to_date (both
     *     required); page_size, cursor (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/get_tracking_number_list.md
     */
    public function getTrackingNumberList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/first_mile/get_tracking_number_list', $params);
    }

    /**
     * Orders from the past 6 months not yet bound to any first-mile code.
     *
     * @param array<string, mixed> $params cursor, page_size,
     *     response_optional_fields (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/get_unbind_order_list.md
     */
    public function getUnbindOrderList(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/first_mile/get_unbind_order_list', $params);
    }

    /**
     * @param array<string, mixed> $params first_mile_tracking_number
     *     (required); cursor (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/FirstMile/get_detail.md
     */
    public function getDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/first_mile/get_detail', $params);
    }

    /**
     * @return string Raw waybill file bytes.
     *
     * @param array<string, mixed> $params first_mile_tracking_number_list
     *     (required, max 50).
     *
     * @see .shopee-docs/API Reference/FirstMile/get_waybill.md
     */
    public function getWaybill(array $params): string
    {
        return $this->client->shopDownloadPost('/api/v2/first_mile/get_waybill', $params);
    }
}
