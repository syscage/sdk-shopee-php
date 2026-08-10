<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Logistics;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Shop pickup/return/default addresses.
 *
 * Accessed via `$shopee->logistics()->address()`.
 *
 * @see .shopee-docs/API Reference/Logistics (get_address_list, update_address, delete_address, set_address_config)
 * @see .docs/api/Logistics/Address.md
 */
final class Address
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/get_address_list.md
     * @return array<string, mixed>
     */
    public function getAddressList(): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_address_list');
    }

    /**
     * The `region` field cannot be changed once set.
     *
     * @see .shopee-docs/API Reference/Logistics/update_address.md
     * @param array<string, mixed> $params address_id (required); state, city, district, town, address, zipcode, name, phone, geo_info (optional).
     * @return array<string, mixed>
     */
    public function updateAddress(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/update_address', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/delete_address.md
     * @param array<string, mixed> $params address_id (required).
     * @return array<string, mixed>
     */
    public function deleteAddress(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/delete_address', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/set_address_config.md
     * @param array<string, mixed> $params show_pickup_address, address_type_config (optional).
     * @return array<string, mixed>
     */
    public function setAddressConfig(array $params = []): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/set_address_config', $params);
    }
}
