<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Product;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Vehicle compatibility lookups for the auto-parts category (Brazil).
 *
 * Accessed via `$shopee->product()->vehicleCompatibility()`.
 *
 * @see .shopee-docs/API Reference/Product/get_all_vehicle_list.md
 * @see .shopee-docs/API Reference/Product/get_vehicle_list_by_compatibility_detail.md
 * @see .docs/api/Product/VehicleCompatibility.md
 */
final class VehicleCompatibility
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_all_vehicle_list.md
     * @param array<string, mixed> $params page_size (required, max 100); offset, language (optional).
     * @return array<string, mixed>
     */
    public function getAllVehicleList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_all_vehicle_list', $params);
    }

    /**
     * Drill down brand -> model -> year -> version.
     *
     * @see .shopee-docs/API Reference/Product/get_vehicle_list_by_compatibility_detail.md
     * @param array<string, mixed> $params compatibility_details (required: Brand|Model|Year|Version); brand_id, model_id, year_id, language (optional, depending on drill-down level).
     * @return array<string, mixed>
     */
    public function getVehicleListByCompatibilityDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_vehicle_list_by_compatibility_detail', $params);
    }
}
