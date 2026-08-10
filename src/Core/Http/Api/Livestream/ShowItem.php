<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Livestream;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * The single item currently spotlighted on-screen during a livestream — a
 * singular pointer/state, not a list, distinct from {@see Item}'s ordered
 * "item bag" even though it's normally set from an item already in the bag.
 *
 * @see .shopee-docs/API Reference/Livestream
 * @see .docs/api/Livestream/ShowItem
 */
final class ShowItem
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params session_id, item_id, shop_id
     *     (all required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/update_show_item.md
     */
    public function updateShowItem(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/update_show_item', $params);
    }

    /**
     * Clears whichever item is currently showing — no item identifier needed.
     *
     * @param array<string, mixed> $params session_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/delete_show_item.md
     */
    public function deleteShowItem(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/delete_show_item', $params);
    }

    /**
     * @param array<string, mixed> $params session_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_show_item.md
     */
    public function getShowItem(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_show_item', $params);
    }
}
