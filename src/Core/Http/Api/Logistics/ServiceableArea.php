<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Logistics;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Brazil "Entrega Turbo" (channel 90026) serviceable-area configuration via
 * KML polygon upload.
 *
 * Accessed via `$shopee->logistics()->serviceableArea()`.
 *
 * @see .shopee-docs/API Reference/Logistics/upload_serviceable_polygon.md
 * @see .shopee-docs/API Reference/Logistics/check_polygon_update_status.md
 * @see .docs/api/Logistics/ServiceableArea.md
 */
final class ServiceableArea
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Upload a `.kml` file defining the shop's serviceable area.
     *
     * Note: Shopee's own documentation header lists the path as
     * `/api/v2/logistics/upload_serviceable_polygon`, but every one of its
     * own request examples (Java/PHP/cURL/Python) calls
     * `/api/v2/logistics/upload_polygon` instead — this implementation uses
     * the path from the examples, since signing a mismatched path would fail
     * with "Wrong sign".
     *
     * @see .shopee-docs/API Reference/Logistics/upload_serviceable_polygon.md
     * @return array<string, mixed>
     */
    public function uploadServiceablePolygon(string $filePath): array
    {
        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException(sprintf('Unable to read file for upload: %s', $filePath));
        }

        return $this->client->shopUpload(
            '/api/v2/logistics/upload_polygon',
            [],
            'file',
            fopen($filePath, 'rb'),
            basename($filePath),
        );
    }

    /**
     * Poll the status of a {@see uploadServiceablePolygon()} task.
     * Status: 0 done, 1 in progress, 2 KML error.
     *
     * @see .shopee-docs/API Reference/Logistics/check_polygon_update_status.md
     * @param array<string, mixed> $params task_id (required).
     * @return array<string, mixed>
     */
    public function checkPolygonUpdateStatus(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/check_polygon_update_status', $params);
    }
}
