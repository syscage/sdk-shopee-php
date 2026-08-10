<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Ams;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Affiliate lookup/discovery, independent of either campaign type's
 * lifecycle:
 *
 * - {@see query()} — general-purpose lookup of any affiliate, by id list
 *   or fuzzy name; the resolver for `affiliate_id`s seen elsewhere in Ams.
 * - {@see getManagedList()} — the seller's own saved/watchlist affiliates.
 * - {@see getRecommendedList()} — Shopee's algorithmic suggestions for
 *   affiliates worth inviting.
 *
 * @see .shopee-docs/API Reference/Ams
 * @see .docs/api/Ams/Affiliate
 */
final class Affiliate
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params query_type (required);
     *     affiliate_id_list, name (optional; max 200 results).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/query_affiliate_list.md
     */
    public function query(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/query_affiliate_list', $params);
    }

    /**
     * The seller's saved/watchlist affiliates (max 2000).
     *
     * @param array<string, mixed> $params page_no, page_size (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_managed_affiliate_list.md
     */
    public function getManagedList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_managed_affiliate_list', $params);
    }

    /**
     * Top 200 Shopee-suggested affiliates (no `page_no`, only `page_size`).
     *
     * @param array<string, mixed> $params page_size (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Ams/get_recommended_affiliate_list.md
     */
    public function getRecommendedList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/ams/get_recommended_affiliate_list', $params);
    }
}
