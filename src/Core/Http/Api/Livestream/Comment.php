<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Livestream;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Session-scoped chat moderation. Bans/unbans mute a user only within the
 * particular session they're issued against (Shopee's error text confirms
 * "The session(session_id:{{sid}}) is not belong to you" applies here) —
 * not an account-wide ban. There is no endpoint to list currently-banned users.
 *
 * @see .shopee-docs/API Reference/Livestream
 * @see .docs/api/Livestream/Comment
 */
final class Comment
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params session_id, content (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/post_comment.md
     */
    public function postComment(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/post_comment', $params);
    }

    /**
     * A rolling ~10-second window of comments — poll repeatedly for a live feed.
     *
     * @param array<string, mixed> $params session_id (required); offset (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_latest_comment_list.md
     */
    public function getLatestCommentList(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_latest_comment_list', $params);
    }

    /**
     * @param array<string, mixed> $params session_id, ban_user_id (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/ban_user_comment.md
     */
    public function banUserComment(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/ban_user_comment', $params);
    }

    /**
     * @param array<string, mixed> $params session_id, unban_user_id (both required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/unban_user_comment.md
     */
    public function unbanUserComment(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/unban_user_comment', $params);
    }
}
