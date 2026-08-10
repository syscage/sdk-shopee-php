<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Product;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Kit item management: items composed of other component items.
 *
 * Accessed via `$shopee->product()->kitItem()`.
 *
 * @see .shopee-docs/API Reference/Product (add_kit_item, update_kit_item, get_kit_item_info, get_kit_item_limit, generate_kit_image)
 * @see .docs/api/Product/KitItem.md
 */
final class KitItem
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Product/add_kit_item.md
     * @param array<string, mixed> $params item_setting (required): item_name, images, logistic_info, model_list, ...; sync_setting (optional).
     * @return array<string, mixed>
     */
    public function addKitItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/add_kit_item', $params);
    }

    /**
     * Cannot delete variations or change component mapping — use for info/setting updates only.
     *
     * @see .shopee-docs/API Reference/Product/update_kit_item.md
     * @param array<string, mixed> $params item_id (required); item_setting, sync_setting (optional).
     * @return array<string, mixed>
     */
    public function updateKitItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/update_kit_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_kit_item_info.md
     * @param array<string, mixed> $params item_id (required).
     * @return array<string, mixed>
     */
    public function getKitItemInfo(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_kit_item_info', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Product/get_kit_item_limit.md
     * @param array<string, mixed> $params category_id (optional).
     * @return array<string, mixed>
     */
    public function getKitItemLimit(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/product/get_kit_item_limit', $params);
    }

    /**
     * Generates a combined cover image from up to 9 component items/models.
     *
     * @see .shopee-docs/API Reference/Product/generate_kit_image.md
     * @param array<string, mixed> $params component_list (required, max 9): [{component_item_id, component_model_id}].
     * @return array<string, mixed>
     */
    public function generateKitImage(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/product/generate_kit_image', $params);
    }
}
