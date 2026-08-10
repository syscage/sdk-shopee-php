<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Logistics;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Shop operating hours (regular, instant, special, and self-collection).
 *
 * Accessed via `$shopee->logistics()->operatingHours()`.
 *
 * @see .shopee-docs/API Reference/Logistics (get_operating_hours, update_operating_hours, get_operating_hour_restrictions, delete_special_operating_hour)
 * @see .docs/api/Logistics/OperatingHours.md
 */
final class OperatingHours
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/get_operating_hours.md
     * @return array<string, mixed>
     */
    public function getOperatingHours(): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_operating_hours');
    }

    /**
     * Full-overwrite semantics: unchanged days/entries must be resent, or
     * they are cleared.
     *
     * @see .shopee-docs/API Reference/Logistics/update_operating_hours.md
     * @param array<string, mixed> $params regular_operating_hour, special_operating_hour, instant_operating_hour, shop_collection_operating_hour (optional).
     * @return array<string, mixed>
     */
    public function updateOperatingHours(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/update_operating_hours', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/get_operating_hour_restrictions.md
     * @return array<string, mixed>
     */
    public function getOperatingHourRestrictions(): array
    {
        return $this->client->shop('GET', '/api/v2/logistics/get_operating_hour_restrictions');
    }

    /**
     * @see .shopee-docs/API Reference/Logistics/delete_special_operating_hour.md
     * @param array<string, mixed> $params name (required).
     * @return array<string, mixed>
     */
    public function deleteSpecialOperatingHour(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/delete_special_operating_hour', $params);
    }
}
