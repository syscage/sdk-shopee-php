<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Returns;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Reverse logistics for a return: eligible carriers, tracking status, and
 * (for TW/BR seller-arranged returns) submitting proof the item was
 * shipped back. `uploadShippingProof()`'s `image_id_list` values come from
 * `Http\Api\Media::uploadImage()` (`business = 2, scene = 1`), NOT from
 * `Proof::convertImage()` — a separate, parallel image pipeline from proof
 * evidence; Shopee's own docs note this endpoint is "not to upload evidence
 * for disputes."
 *
 * @see .shopee-docs/API Reference/Returns
 * @see .docs/api/Returns/Shipping
 */
final class Shipping
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * TW/BR seller-arrange returns only.
     *
     * @param array<string, mixed> $params return_sn (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/get_shipping_carrier.md
     */
    public function getShippingCarrier(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/returns/get_shipping_carrier', $params);
    }

    /**
     * @param array<string, mixed> $params return_sn (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/get_reverse_tracking_info.md
     */
    public function getReverseTrackingInfo(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/returns/get_reverse_tracking_info', $params);
    }

    /**
     * @param array<string, mixed> $params return_sn,
     *     reverse_logistics_carrier_id (both required);
     *     reverse_logistics_carrier_name, tracking_number, image_id_list,
     *     remarks (optional; `image_id_list[].image_id` values come from
     *     `Http\Api\Media::uploadImage()`).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/upload_shipping_proof.md
     */
    public function uploadShippingProof(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/returns/upload_shipping_proof', $params);
    }
}
