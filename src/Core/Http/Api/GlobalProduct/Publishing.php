<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\GlobalProduct;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Publishing a global item into regional shops.
 *
 * Accessed via `$shopee->globalProduct()->publishing()`.
 *
 * @see .shopee-docs/API Reference/GlobalProduct (create_publish_task, get_publish_task_result, get_publishable_shop, get_published_list, get_shop_publishable_status, set_sync_field)
 * @see .docs/api/GlobalProduct/Publishing.md
 */
final class Publishing
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Publish a global item into one regional shop. Async — returns a
     * `publish_task_id`; poll {@see getPublishTaskResult()} for the outcome.
     *
     * @see .shopee-docs/API Reference/GlobalProduct/create_publish_task.md
     * @param array<string, mixed> $params global_item_id, shop_id, shop_region (required); item (optional, per-shop overrides).
     * @return array<string, mixed>
     */
    public function createPublishTask(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/create_publish_task', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_publish_task_result.md
     * @param array<string, mixed> $params publish_task_id (required).
     * @return array<string, mixed>
     */
    public function getPublishTaskResult(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_publish_task_result', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_publishable_shop.md
     * @param array<string, mixed> $params global_item_id (required); shop_id_list (optional).
     * @return array<string, mixed>
     */
    public function getPublishableShop(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_publishable_shop', $params);
    }

    /**
     * @see .shopee-docs/API Reference/GlobalProduct/get_published_list.md
     * @param array<string, mixed> $params global_item_id (required); shop_id_list (optional).
     * @return array<string, mixed>
     */
    public function getPublishedList(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_published_list', $params);
    }

    /**
     * Paginated variant of {@see getPublishableShop()} that also reports why
     * a shop is blocked from publishing, if it is.
     *
     * @see .shopee-docs/API Reference/GlobalProduct/get_shop_publishable_status.md
     * @param array<string, mixed> $params global_item_id, offset, page_size (max 100) (required).
     * @return array<string, mixed>
     */
    public function getShopPublishableStatus(array $params): array
    {
        return $this->client->merchant('GET', '/api/v2/global_product/get_shop_publishable_status', $params);
    }

    /**
     * Configure which fields auto-sync from the global item to its published
     * regional shops (name/description, media, tier-variation names, price, days-to-ship).
     *
     * @see .shopee-docs/API Reference/GlobalProduct/set_sync_field.md
     * @param array<string, mixed> $params shop_sync_list (required, max 50): [{shop_id, shop_region, name_and_description, media_information, tier_variation_name_and_option, price, days_to_ship}].
     * @return array<string, mixed>
     */
    public function setSyncField(array $params): array
    {
        return $this->client->merchant('POST', '/api/v2/global_product/set_sync_field', $params);
    }
}
