<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Media API domain (`v2.media.*`) — a newer, business/scene-parameterized
 * upload API, distinct from {@see MediaSpace} (`v2.media_space.*`). Where
 * MediaSpace uploads a generic item image/video, Media uploads are scoped
 * to a specific `business` (e.g. 2 = Returns) and `scene` within it (e.g.
 * 1 = Return Seller Self Arrange Pickup Proof Image).
 *
 * Every endpoint in this domain signs at the **Public** level (no
 * `access_token`/`shop_id` at all) — including the video session lifecycle
 * calls, which is a further difference from MediaSpace, where the
 * equivalent init/get-result/cancel calls are Shop-level.
 *
 * @see .shopee-docs/API Reference/Media
 * @see .docs/api/Media
 */
final class Media
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Upload one or more images for a given business/scene. Currently only
     * business=2 (Returns), scene=1 (Return Seller Self Arrange Pickup Proof
     * Image) is documented: up to 3 images, 10MB each, JPG/JPEG/PNG.
     *
     * @param string[] $filePaths
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Media/upload_image.md
     */
    public function uploadImage(array $filePaths, int $business, int $scene): array
    {
        if ($filePaths === []) {
            throw new \InvalidArgumentException('At least one image file path is required.');
        }

        $fileParts = array_map(function (string $filePath): array {
            if (!is_readable($filePath)) {
                throw new \InvalidArgumentException(sprintf('Unable to read file for upload: %s', $filePath));
            }

            return ['name' => 'images', 'contents' => fopen($filePath, 'rb'), 'filename' => basename($filePath)];
        }, $filePaths);

        return $this->client->publicUpload(
            '/api/v2/media/upload_image',
            ['business' => $business, 'scene' => $scene],
            $fileParts,
        );
    }

    /**
     * Initialize a video upload session and get the required per-part size.
     * Currently only business=3 (Video), scene=1 (Shopee Video) is
     * documented: max 1GB, 1–180 seconds.
     *
     * @see .shopee-docs/API Reference/Media/init_video_upload.md
     * @param array<string, mixed> $params business, scene, file_name, file_size, duration (required).
     * @return array<string, mixed>
     */
    public function initVideoUpload(array $params): array
    {
        return $this->client->public('POST', '/api/v2/media/init_video_upload', $params);
    }

    /**
     * Upload one part of a video. Part size must exactly match `part_size`
     * from {@see initVideoUpload()}, except the last part.
     *
     * @see .shopee-docs/API Reference/Media/upload_video_part.md
     */
    public function uploadVideoPart(string $videoUploadId, int $partSeq, string $partMd5, string $filePath): array
    {
        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException(sprintf('Unable to read file for upload: %s', $filePath));
        }

        return $this->client->publicUpload(
            '/api/v2/media/upload_video_part',
            ['video_upload_id' => $videoUploadId, 'part_seq' => $partSeq, 'part_md5' => $partMd5],
            [['name' => 'part_content', 'contents' => fopen($filePath, 'rb'), 'filename' => basename($filePath)]],
        );
    }

    /**
     * Finalize the upload once every part has been sent; starts transcoding.
     *
     * @see .shopee-docs/API Reference/Media/complete_video_upload.md
     * @param array<string, mixed> $params video_upload_id (required).
     * @return array<string, mixed>
     */
    public function completeVideoUpload(array $params): array
    {
        return $this->client->public('POST', '/api/v2/media/complete_video_upload', $params);
    }

    /**
     * Poll status: INITIATED, UPLOADING, UPLOADED, PROCESSING, SUCCEEDED,
     * FAILED, CANCELLED.
     *
     * @see .shopee-docs/API Reference/Media/get_video_upload_result.md
     * @param array<string, mixed> $params video_upload_id (required).
     * @return array<string, mixed>
     */
    public function getVideoUploadResult(array $params): array
    {
        return $this->client->public('GET', '/api/v2/media/get_video_upload_result', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Media/cancel_video_upload.md
     * @param array<string, mixed> $params video_upload_id (required).
     * @return array<string, mixed>
     */
    public function cancelVideoUpload(array $params): array
    {
        return $this->client->public('POST', '/api/v2/media/cancel_video_upload', $params);
    }
}
