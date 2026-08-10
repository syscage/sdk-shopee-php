<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * TopPicks API domain (`v2.top_picks.*`) — manage a shop's "Top Picks" item
 * collections (curated groups of items shown to buyers). Flat CRUD, same
 * shape as {@see Voucher}/{@see FollowPrize}: no sub-accessor needed.
 *
 * Every endpoint signs at the **Shop** level.
 *
 * @see .shopee-docs/API Reference/TopPicks
 * @see .docs/api/TopPicks
 */
final class TopPicks
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params name, item_id_list, is_activated (all required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/TopPicks/add_top_picks.md
     */
    public function addTopPicks(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/top_picks/add_top_picks', $params);
    }

    /**
     * @param array<string, mixed> $params top_picks_id (required), name,
     *     item_id_list, is_activated (all optional — item_id_list replaces
     *     the existing list wholesale, it does not merge).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/TopPicks/update_top_picks.md
     */
    public function updateTopPicks(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/top_picks/update_top_picks', $params);
    }

    /**
     * @param array<string, mixed> $params top_picks_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/TopPicks/delete_top_picks.md
     */
    public function deleteTopPicks(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/top_picks/delete_top_picks', $params);
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/TopPicks/get_top_picks_list.md
     */
    public function getTopPicksList(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/top_picks/get_top_picks_list', $params);
    }
}
