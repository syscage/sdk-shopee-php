<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\Livestream\Comment;
use Syscage\Sdk\Shopee\Core\Http\Api\Livestream\Item;
use Syscage\Sdk\Shopee\Core\Http\Api\Livestream\ItemSet;
use Syscage\Sdk\Shopee\Core\Http\Api\Livestream\ShowItem;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Livestream API domain (`v2.livestream.*`) — Shopee's live-selling video
 * streaming feature. A "session" is one livestream broadcast, with a
 * simple state machine: {@see createSession()} (Initial) →
 * {@see startSession()} (Ongoing, needs `domain_id` from
 * {@see getSessionDetail()}) → {@see endSession()} (Ended). Only one
 * session can be ongoing at a time. This class covers session
 * lifecycle/metadata/reporting and the cover-image upload; four sub-
 * concepts with genuinely distinct key spaces/workflows are split out:
 *
 * - {@see Item} (`->item()`) — the session's ordered "item bag"
 *   (`{item_id, shop_id}` pairs), plus account-level item-picker feeds.
 * - {@see ShowItem} (`->showItem()`) — the single item currently
 *   spotlighted on-screen; a singular pointer, not a list, distinct from
 *   the item bag even though it's normally drawn from it.
 * - {@see ItemSet} (`->itemSet()`) — pre-existing, named, reusable
 *   collections (`item_set_id`) that can be bulk-merged into a session's
 *   item bag; read-only from this API (managed via Seller Center).
 * - {@see Comment} (`->comment()`) — session-scoped chat moderation.
 *
 * Every endpoint signs at the **User** level, same as {@see Video}.
 *
 * @see .shopee-docs/API Reference/Livestream
 * @see .docs/api/Livestream
 */
final class Livestream
{
    private ?Item $item = null;
    private ?ShowItem $showItem = null;
    private ?ItemSet $itemSet = null;
    private ?Comment $comment = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function item(): Item
    {
        return $this->item ??= new Item($this->client);
    }

    public function showItem(): ShowItem
    {
        return $this->showItem ??= new ShowItem($this->client);
    }

    public function itemSet(): ItemSet
    {
        return $this->itemSet ??= new ItemSet($this->client);
    }

    public function comment(): Comment
    {
        return $this->comment ??= new Comment($this->client);
    }

    /**
     * `cover_image_url` should come from {@see uploadImage()}.
     *
     * @param array<string, mixed> $params title, cover_image_url (both
     *     required); description, is_test (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/create_session.md
     */
    public function createSession(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/create_session', $params);
    }

    /**
     * `domain_id` comes from {@see getSessionDetail()}'s `stream_url_list`.
     *
     * @param array<string, mixed> $params session_id, domain_id (both
     *     required); ai_stream (optional, PH only).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/start_session.md
     */
    public function startSession(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/start_session', $params);
    }

    /**
     * @param array<string, mixed> $params session_id, title,
     *     cover_image_url, is_test (all required); description (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/update_session.md
     */
    public function updateSession(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/update_session', $params);
    }

    /**
     * @param array<string, mixed> $params session_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/end_session.md
     */
    public function endSession(array $params): array
    {
        return $this->client->user('POST', '/api/v2/livestream/end_session', $params);
    }

    /**
     * @param array<string, mixed> $params session_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_session_detail.md
     */
    public function getSessionDetail(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_session_detail', $params);
    }

    /**
     * @param array<string, mixed> $params session_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_session_metric.md
     */
    public function getSessionMetric(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_session_metric', $params);
    }

    /**
     * @param array<string, mixed> $params session_id, offset, page_size
     *     (all required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/get_session_item_metric.md
     */
    public function getSessionItemMetric(array $params): array
    {
        return $this->client->user('GET', '/api/v2/livestream/get_session_item_metric', $params);
    }

    /**
     * Upload a session cover image, returning an `image_url` for
     * {@see createSession()}/{@see updateSession()}'s `cover_image_url`.
     *
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Livestream/upload_image.md
     */
    public function uploadImage(string $filePath): array
    {
        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException(sprintf('Unable to read file for upload: %s', $filePath));
        }

        return $this->client->userUpload(
            '/api/v2/livestream/upload_image',
            [],
            'image',
            fopen($filePath, 'rb'),
            basename($filePath),
        );
    }
}
