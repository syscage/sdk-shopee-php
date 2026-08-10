<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Livestream;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * A session's ordered "item bag" — `{item_id, shop_id}` pairs referencing
 * the regular product catalog, shown/pinned during a livestream. Distinct
 * from {@see ShowItem} (the single item currently spotlighted on-screen).
 * `getRecentItemList()`/`getLikeItemList()` take no `session_id` — they're
 * account-level item-picker feeds ("Recently", "My Likes") meant to help
 * populate the bag via {@see addItemList()}, not bag contents themselves.
 *
 * @see .shopee-docs/API Reference/Livestream
 * @see .docs/api/Livestream/Item
 */
final class Item
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params session_id, item_list (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/add_item_list.md
     */
    public function addItemList(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/add_item_list', $params);
    }

    /**
     * Reorders the bag — `item_list` must match the full current set
     * (Shopee errors if used to add or remove items).
     *
     * @param array<string, mixed> $params session_id, item_list (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/update_item_list.md
     */
    public function updateItemList(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/update_item_list', $params);
    }

    /**
     * @param array<string, mixed> $params session_id, item_list (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/delete_item_list.md
     */
    public function deleteItemList(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/delete_item_list', $params);
    }

    /**
     * @param array<string, mixed> $params session_id, offset, page_size
     *     (all required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_item_list.md
     */
    public function getItemList(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_item_list', $params);
    }

    /**
     * @param array<string, mixed> $params session_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_item_count.md
     */
    public function getItemCount(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_item_count', $params);
    }

    /**
     * Account-level "Recently" item-picker tab — no `session_id`.
     *
     * @param array<string, mixed> $params offset, page_size (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_recent_item_list.md
     */
    public function getRecentItemList(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_recent_item_list', $params);
    }

    /**
     * Account-level "My Likes" item-picker tab — no `session_id`.
     *
     * @param array<string, mixed> $params offset, page_size (both
     *     required); keyword (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_like_item_list.md
     */
    public function getLikeItemList(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_like_item_list', $params);
    }
}
