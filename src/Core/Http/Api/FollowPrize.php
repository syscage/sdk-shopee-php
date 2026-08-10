<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Follow Prize API domain — shop-level voucher rewarded to buyers for
 * following the shop. Same flat CRUD shape as {@see Voucher}, no
 * sub-accessor needed.
 *
 * Note: despite the domain being "Follow Prize", the identifier field
 * throughout Shopee's own API is `campaign_id`, not `follow_prize_id` — kept
 * as documented rather than renamed, so request arrays match Shopee's docs.
 *
 * @see .shopee-docs/API Reference/Follow Prize
 * @see .docs/api/FollowPrize
 */
final class FollowPrize
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Start time must be in the future; end time at least 1 day after start,
     * and start/end must not overlap another upcoming/ongoing follow prize.
     *
     * @see .shopee-docs/API Reference/Follow Prize/add_follow_prize.md
     * @param array<string, mixed> $params follow_prize_name (max 20 chars), start_time, end_time, usage_quantity, min_spend, reward_type (1=fix_amount, 2=discount_percentage, 3=coin_cashback) (required); discount_amount, percentage, max_price (optional, depending on reward_type).
     * @return array<string, mixed>
     */
    public function addFollowPrize(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/follow_prize/add_follow_prize', $params);
    }

    /**
     * Several fields (name, min_spend, start_time) can no longer change once
     * ongoing; quantity can only increase; end_time can only move earlier.
     *
     * @see .shopee-docs/API Reference/Follow Prize/update_follow_prize.md
     * @param array<string, mixed> $params campaign_id (required); follow_prize_name, start_time, end_time, usage_quantity, min_spend (optional).
     * @return array<string, mixed>
     */
    public function updateFollowPrize(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/follow_prize/update_follow_prize', $params);
    }

    /**
     * Only an upcoming follow prize can be deleted.
     *
     * @see .shopee-docs/API Reference/Follow Prize/delete_follow_prize.md
     * @param array<string, mixed> $params campaign_id (required).
     * @return array<string, mixed>
     */
    public function deleteFollowPrize(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/follow_prize/delete_follow_prize', $params);
    }

    /**
     * Only an ongoing follow prize can be ended.
     *
     * @see .shopee-docs/API Reference/Follow Prize/end_follow_prize.md
     * @param array<string, mixed> $params campaign_id (required).
     * @return array<string, mixed>
     */
    public function endFollowPrize(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/follow_prize/end_follow_prize', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Follow Prize/get_follow_prize_detail.md
     * @param array<string, mixed> $params campaign_id (optional).
     * @return array<string, mixed>
     */
    public function getFollowPrizeDetail(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/follow_prize/get_follow_prize_detail', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Follow Prize/get_follow_prize_list.md
     * @param array<string, mixed> $params status (required: upcoming/ongoing/expired/all); page_no, page_size (optional).
     * @return array<string, mixed>
     */
    public function getFollowPrizeList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/follow_prize/get_follow_prize_list', $params);
    }
}
