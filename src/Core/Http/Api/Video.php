<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\Video\Analytics;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Video API domain (`v2.video.*`) — Shopee Video content management.
 * Analytics/performance reporting (9 endpoints) is split into
 * {@see Analytics} (via {@see analytics()}): it's a fully separate concern
 * from content management (no side effects, its own date-range/pagination
 * shapes), not a variant of it.
 *
 * **Distinct from, and downstream of, {@see \Syscage\Sdk\Shopee\Core\Http\Api\Media}**
 * — raw video upload happens entirely through `Media::initVideoUpload()` /
 * `uploadVideoPart()` / `completeVideoUpload()` / `getVideoUploadResult()`
 * (`business = 3, scene = 1`). This class never uploads bytes; it consumes
 * the resulting `video_upload_id` to set metadata ({@see editVideoInfo()}),
 * choose a cover frame ({@see getCoverList()}), and publish
 * ({@see postVideo()}). Confirmed by Shopee's own doc text on both
 * `post_video` and `edit_video_info`, which explicitly describe the Media
 * upload flow as a prerequisite step.
 *
 * Every endpoint signs at a **fifth auth level, User**
 * (`access_token` + `user_id`, not shop/merchant/principal) — see
 * {@see Client::user()}.
 *
 * @see .shopee-docs/API Reference/Video
 * @see .docs/api/Video
 */
final class Video
{
    private ?Analytics $analytics = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function analytics(): Analytics
    {
        return $this->analytics ??= new Analytics($this->client);
    }

    /**
     * @param array<string, mixed> $params page_no, page_size, list_type
     *     (all required; list_type: 1 = draft, 2 = posted).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_video_list.md
     */
    public function getVideoList(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_video_list', $params);
    }

    /**
     * Exactly one of `video_upload_id` (draft) or `post_id` (published)
     * must be given — Shopee's doc marks both optional but requires one.
     *
     * @param array<string, mixed> $params video_upload_id, post_id (exactly one).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_video_detail.md
     */
    public function getVideoDetail(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_video_detail', $params);
    }

    /**
     * Choose a cover frame for a still-draft video (from a video already
     * uploaded via `Media`), to feed into {@see editVideoInfo()}'s
     * `cover_image_url`.
     *
     * @param array<string, mixed> $params video_upload_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/get_cover_list.md
     */
    public function getCoverList(array $params): array
    {
        return $this->client->user('GET', '/api/v2/video/get_cover_list', $params);
    }

    /**
     * Set a draft video's metadata (caption, cover, tagged items,
     * duet/stitch permissions, scheduled post time) before
     * {@see postVideo()}. The video remains draft after this call.
     *
     * @param array<string, mixed> $params video_upload_list (required, max
     *     5 — each: video_upload_id, allow_info, scheduled_info required,
     *     cover_image_url optional); caption, item_info (optional, max 6 items).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/edit_video_info.md
     */
    public function editVideoInfo(array $params): array
    {
        return $this->client->user('POST', '/api/v2/video/edit_video_info', $params);
    }

    /**
     * Publish one or more draft videos (already uploaded via `Media` and
     * described via {@see editVideoInfo()}) to Shopee Video.
     *
     * @param array<string, mixed> $params video_upload_id_list (required, max 5).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/post_video.md
     */
    public function postVideo(array $params): array
    {
        return $this->client->user('POST', '/api/v2/video/post_video', $params);
    }

    /**
     * Works for either a draft or a posted video.
     *
     * @param array<string, mixed> $params video_upload_id_list, post_id_list
     *     (exactly one).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Video/delete_video.md
     */
    public function deleteVideo(array $params): array
    {
        return $this->client->user('POST', '/api/v2/video/delete_video', $params);
    }
}
