<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\Logistics\Address;
use Syscage\Sdk\Shopee\Core\Http\Api\Logistics\Booking;
use Syscage\Sdk\Shopee\Core\Http\Api\Logistics\OperatingHours;
use Syscage\Sdk\Shopee\Core\Http\Api\Logistics\ServiceableArea;
use Syscage\Sdk\Shopee\Core\Http\Api\Logistics\ShippingDocument;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Logistics API domain.
 *
 * Covers shipping parameters/channels, arranging shipment for an order, and
 * tracking — the things you do to ship an order right now.
 *
 * Longer-lived resources and distinct sub-workflows live behind their own
 * accessor instead of bloating this class further:
 * {@see address()}, {@see operatingHours()}, {@see shippingDocument()},
 * {@see serviceableArea()}, {@see booking()}.
 *
 * @see .shopee-docs/API Reference/Logistics
 * @see .docs/api/Logistics
 */
final class Logistics
{
    private ?Address $address = null;
    private ?Booking $booking = null;
    private ?OperatingHours $operatingHours = null;
    private ?ServiceableArea $serviceableArea = null;
    private ?ShippingDocument $shippingDocument = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function address(): Address
    {
        return $this->address ??= new Address($this->client);
    }

    public function booking(): Booking
    {
        return $this->booking ??= new Booking($this->client);
    }

    public function operatingHours(): OperatingHours
    {
        return $this->operatingHours ??= new OperatingHours($this->client);
    }

    public function serviceableArea(): ServiceableArea
    {
        return $this->serviceableArea ??= new ServiceableArea($this->client);
    }

    public function shippingDocument(): ShippingDocument
    {
        return $this->shippingDocument ??= new ShippingDocument($this->client);
    }

    // ------------------------------------------------------------------
    // Shipping Parameters & Channels
    // ------------------------------------------------------------------

    /**
     * Check available pickup/dropoff/non-integrated options for an order.
     *
     * @see .shopee-docs/API Reference/Logistics/get_shipping_parameter.md
     * @param array<string, mixed> $params order_sn (required); package_number (optional).
     * @return array<string, mixed>
     */
    public function getShippingParameter(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_shipping_parameter', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/get_channel_list.md
     * @return array<string, mixed>
     */
    public function getChannelList(): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_channel_list');
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/update_channel.md
     * @param array<string, mixed> $params logistics_channel_id (required); enabled, cod_enabled, auto_call_driver_setting (optional).
     * @return array<string, mixed>
     */
    public function updateChannel(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/update_channel', $params);
    }

    /**
     * Batch shipping-parameter check for packages sharing one warehouse and channel.
     *
     * @see .shopee-docs/API Reference/Logistics/get_mass_shipping_parameter.md
     * @param array<string, mixed> $params package_list (required); logistics_channel_id, product_location_id (optional).
     * @return array<string, mixed>
     */
    public function getMassShippingParameter(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/get_mass_shipping_parameter', $params);
    }

    // ------------------------------------------------------------------
    // Ship Order
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/Logistics/ship_order.md
     * @param array<string, mixed> $params order_sn (required); package_number, pickup, dropoff, non_integrated (optional).
     * @return array<string, mixed>
     */
    public function shipOrder(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/ship_order', $params);
    }

    /**
     * Brazil channel 90003 only.
     *
     * @see .shopee-docs/API Reference/Logistics/batch_ship_order.md
     * @param array<string, mixed> $params order_list (required); pickup, dropoff, non_integrated (optional).
     * @return array<string, mixed>
     */
    public function batchShipOrder(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/batch_ship_order', $params);
    }

    /**
     * Batch arrange shipment for packages under the same warehouse and channel.
     *
     * @see .shopee-docs/API Reference/Logistics/mass_ship_order.md
     * @param array<string, mixed> $params package_list (required); logistics_channel_id, product_location_id, pickup, dropoff, non_integrated (optional).
     * @return array<string, mixed>
     */
    public function massShipOrder(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/mass_ship_order', $params);
    }

    /**
     * Reschedule the pickup address/time for a package.
     *
     * @see .shopee-docs/API Reference/Logistics/update_shipping_order.md
     * @param array<string, mixed> $params order_sn, pickup (required); package_number (optional).
     * @return array<string, mixed>
     */
    public function updateShippingOrder(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/update_shipping_order', $params);
    }

    /**
     * Mark a self-collection (pharmacy) order ready/collected. Proof images
     * are passed as pre-uploaded image ids (`v2.media.upload_image`), not raw files.
     *
     * @see .shopee-docs/API Reference/Logistics/update_self_collection_order_logistics.md
     * @param array<string, mixed> $params package_number, self_collection_logistics_action (required); epoc_image_list, pin (optional).
     * @return array<string, mixed>
     */
    public function updateSelfCollectionOrderLogistics(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/update_self_collection_order_logistics', $params);
    }

    /**
     * Brazil channels 90021/90025/90026 only.
     *
     * @see .shopee-docs/API Reference/Logistics/update_tracking_status.md
     * @param array<string, mixed> $params order_sn, logistics_status (required); tracking_number, tracking_url, failed_reason (optional).
     * @return array<string, mixed>
     */
    public function updateTrackingStatus(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/update_tracking_status', $params);
    }

    /**
     * 3PF warehouse vendors report cross-border order intake/outbound status.
     *
     * @see .shopee-docs/API Reference/Logistics/batch_update_tpf_warehouse_tracking_status.md
     * @param array<string, mixed> $params tpf_name, tpf_tracking_status, package_list (required).
     * @return array<string, mixed>
     */
    public function batchUpdateTpfWarehouseTrackingStatus(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/batch_update_tpf_warehouse_tracking_status', $params);
    }

    // ------------------------------------------------------------------
    // Tracking
    // ------------------------------------------------------------------

    /**
     * May return empty until Shopee assigns one — poll roughly every 5 minutes.
     *
     * @see .shopee-docs/API Reference/Logistics/get_tracking_number.md
     * @param array<string, mixed> $params order_sn (required); package_number, response_optional_fields (optional).
     * @return array<string, mixed>
     */
    public function getTrackingNumber(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_tracking_number', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/get_tracking_info.md
     * @param array<string, mixed> $params order_sn (required); package_number (optional).
     * @return array<string, mixed>
     */
    public function getTrackingInfo(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_tracking_info', $params);
    }

    /**
     * Batch tracking-number lookup after {@see massShipOrder()}.
     *
     * @see .shopee-docs/API Reference/Logistics/get_mass_tracking_number.md
     * @param array<string, mixed> $params package_list (required); response_optional_fields (optional).
     * @return array<string, mixed>
     */
    public function getMassTrackingNumber(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/get_mass_tracking_number', $params);
    }

    // ------------------------------------------------------------------
    // Pause Status
    // ------------------------------------------------------------------

    /**
     * @see .shopee-docs/API Reference/Logistics/get_pause_status.md
     * @return array<string, mixed>
     */
    public function getPauseStatus(): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_pause_status');
    }

    /**
     * Pause/resume incoming orders on paused channels. Takes effect with a
     * ~15 second propagation delay.
     *
     * @see .shopee-docs/API Reference/Logistics/set_pause_status.md
     * @param array<string, mixed> $params is_paused (required).
     * @return array<string, mixed>
     */
    public function setPauseStatus(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/set_pause_status', $params);
    }

    // ------------------------------------------------------------------
    // Mart Packaging
    // ------------------------------------------------------------------

    /**
     * ID Mart sellers only.
     *
     * @see .shopee-docs/API Reference/Logistics/get_mart_packaging_info.md
     * @return array<string, mixed>
     */
    public function getMartPackagingInfo(): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_mart_packaging_info');
    }

    /**
     * ID Mart sellers only.
     *
     * @see .shopee-docs/API Reference/Logistics/set_mart_packaging_info.md
     * @param array<string, mixed> $params enable (required); dimension, packaging_fee (required if enabled).
     * @return array<string, mixed>
     */
    public function setMartPackagingInfo(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/set_mart_packaging_info', $params);
    }
}
