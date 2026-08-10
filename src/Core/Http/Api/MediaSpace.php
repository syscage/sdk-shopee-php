<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * MediaSpace API domain — image and video upload for use in item listings.
 *
 * Auth is mixed per endpoint (unusually for a single domain): image upload
 * and video-part upload sign at the **Public** level (no access token at
 * all — the uploaded media isn't tied to a shop until it's referenced by an
 * item), while initiating/querying/cancelling a video upload session are
 * **Shop**-level. Each method uses whichever the endpoint's own
 * documentation specifies rather than assuming domain-wide consistency.
 *
 * @see .shopee-docs/API Reference/MediaSpace
 * @see .docs/api/MediaSpace
 */
final class MediaSpace
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Upload up to 9 images (max 10MB each, JPG/JPEG/PNG). Public-level —
     * no access token required.
     *
     * @param string[] $filePaths
     * @param string|null $scene "normal" (default, square-cropped — item images) or "desc" (uncropped — description images).
     * @param string|null $ratio "1:1" (default) or "3:4" — whitelisted sellers only.
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/MediaSpace/upload_image.md
     */
    public function uploadImage(array $filePaths, ?string $scene = null, ?string $ratio = null): array
    {
        if ($filePaths === []) {
            throw new \InvalidArgumentException('At least one image file path is required.');
        }

        $fields = array_filter(['scene' => $scene, 'ratio' => $ratio], static fn (?string $v) => $v !== null);

        $fileParts = array_map(function (string $filePath): array {
            if (!is_readable($filePath)) {
                throw new \InvalidArgumentException(sprintf('Unable to read file for upload: %s', $filePath));
            }

            return ['name' => 'image', 'contents' => fopen($filePath, 'rb'), 'filename' => basename($filePath)];
        }, $filePaths);

        return $this->client->publicUpload('/api/v2/media_space/upload_image', $fields, $fileParts);
    }

    /**
     * Initiate a video upload session. Video duration must be 10–60 seconds.
     *
     * @see .shopee-docs/API Reference/MediaSpace/init_video_upload.md
     * @param array<string, mixed> $params file_md5, file_size (required, max 30MB).
     * @return array<string, mixed>
     */
    public function initVideoUpload(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/media_space/init_video_upload', $params);
    }

    /**
     * Upload one part of a video (exactly 4MB, except the last part).
     * Public-level — no access token required.
     *
     * @see .shopee-docs/API Reference/MediaSpace/upload_video_part.md
     */
    public function uploadVideoPart(string $videoUploadId, int $partSeq, string $contentMd5, string $filePath): array
    {
        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException(sprintf('Unable to read file for upload: %s', $filePath));
        }

        return $this->client->publicUpload(
            '/api/v2/media_space/upload_video_part',
            ['video_upload_id' => $videoUploadId, 'part_seq' => $partSeq, 'content_md5' => $contentMd5],
            [['name' => 'part_content', 'contents' => fopen($filePath, 'rb'), 'filename' => basename($filePath)]],
        );
    }

    /**
     * Finish the upload once every part has been sent; starts transcoding.
     * Public-level — no access token required.
     *
     * @see .shopee-docs/API Reference/MediaSpace/complete_video_upload.md
     * @param array<string, mixed> $params video_upload_id, part_seq_list, report_data (required: {upload_cost}).
     * @return array<string, mixed>
     */
    public function completeVideoUpload(array $params): array
    {
        return $this->client->public('POST', '/api/v2/media_space/complete_video_upload', $params);
    }

    /**
     * Poll transcoding status: INITIATED, TRANSCODING, SUCCEEDED, FAILED, CANCELLED.
     *
     * @see .shopee-docs/API Reference/MediaSpace/get_video_upload_result.md
     * @param array<string, mixed> $params video_upload_id (required).
     * @return array<string, mixed>
     */
    public function getVideoUploadResult(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/media_space/get_video_upload_result', $params);
    }

    /**
     * @see .shopee-docs/API Reference/MediaSpace/cancel_video_upload.md
     * @param array<string, mixed> $params video_upload_id (required).
     * @return array<string, mixed>
     */
    public function cancelVideoUpload(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/media_space/cancel_video_upload', $params);
    }
}
