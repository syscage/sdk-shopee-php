<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Livestream;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Pre-existing, named, reusable item collections (`item_set_id`) — managed
 * via Seller Center, read-only from this API (no create/edit endpoints
 * here). {@see applyItemSet()} bulk-merges a set's items directly into a
 * session's item bag ({@see Item}) in one call.
 *
 * @see .shopee-docs/API Reference/Livestream
 * @see .docs/api/Livestream/ItemSet
 */
final class ItemSet
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params offset, page_size (both
     *     required); keyword (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_item_set_list.md
     */
    public function getItemSetList(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_item_set_list', $params);
    }

    /**
     * @param array<string, mixed> $params item_set_id, offset, page_size
     *     (all required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_item_set_item_list.md
     */
    public function getItemSetItemList(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_item_set_item_list', $params);
    }

    /**
     * @param array<string, mixed> $params session_id, item_set_ids (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/apply_item_set.md
     */
    public function applyItemSet(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/apply_item_set', $params);
    }
}
