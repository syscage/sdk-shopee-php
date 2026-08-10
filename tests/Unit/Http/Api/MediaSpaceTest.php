<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\MediaSpace;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class MediaSpaceTest extends TestCase
{
    use InteractsWithMockHttp;

    private function withTempFile(string $contents, callable $callback): mixed
    {
        $path = tempnam(sys_get_temp_dir(), 'shopee-sdk-test-');
        file_put_contents($path, $contents);

        try {
            return $callback($path);
        } finally {
            unlink($path);
        }
    }

    public function testUploadImageSendsPublicMultipartRequestWithoutAccessToken(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        $this->withTempFile('fake jpeg bytes', function (string $path) use ($client): void {
            (new MediaSpace($client))->uploadImage([$path], scene: 'desc', ratio: '3:4');

            $request = $this->lastRequest();
            $this->assertSame('POST', $request->getMethod());
            $this->assertSame('/api/v2/media_space/upload_image', $request->getUri()->getPath());
            $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));

            $body = (string) $request->getBody();
            $this->assertStringContainsString('name="scene"', $body);
            $this->assertStringContainsString('name="ratio"', $body);
            $this->assertStringContainsString('name="image"; filename="' . basename($path) . '"', $body);
            $this->assertStringContainsString('fake jpeg bytes', $body);

            $query = $this->lastRequestQuery();
            $this->assertArrayHasKey('partner_id', $query);
            $this->assertArrayNotHasKey('access_token', $query);
            $this->assertArrayNotHasKey('shop_id', $query);
        });
    }

    public function testUploadImageSupportsMultipleFiles(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        $pathA = tempnam(sys_get_temp_dir(), 'shopee-sdk-test-a-');
        $pathB = tempnam(sys_get_temp_dir(), 'shopee-sdk-test-b-');
        file_put_contents($pathA, 'image a');
        file_put_contents($pathB, 'image b');

        try {
            (new MediaSpace($client))->uploadImage([$pathA, $pathB]);

            $body = (string) $this->lastRequest()->getBody();
            $this->assertStringContainsString('filename="' . basename($pathA) . '"', $body);
            $this->assertStringContainsString('filename="' . basename($pathB) . '"', $body);
        } finally {
            unlink($pathA);
            unlink($pathB);
        }
    }

    public function testUploadImageThrowsWhenNoFilesGiven(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new MediaSpace($this->makeClient([])))->uploadImage([]);
    }

    public function testUploadImageThrowsOnUnreadableFile(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new MediaSpace($this->makeClient([])))->uploadImage(['/nonexistent/path/does-not-exist.jpg']);
    }

    public function testInitVideoUploadUsesShopLevelAuth(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new MediaSpace($client))->initVideoUpload(['file_md5' => 'abc', 'file_size' => 123]);

        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/media_space/init_video_upload', $this->lastRequest()->getUri()->getPath());
        $this->assertSame('14701711', $this->lastRequestQuery()['shop_id']);
    }

    public function testUploadVideoPartSendsPublicMultipartRequest(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        $this->withTempFile('fake video part bytes', function (string $path) use ($client): void {
            (new MediaSpace($client))->uploadVideoPart('vid-1', 0, 'md5hash', $path);

            $request = $this->lastRequest();
            $this->assertSame('POST', $request->getMethod());
            $this->assertSame('/api/v2/media_space/upload_video_part', $request->getUri()->getPath());

            $body = (string) $request->getBody();
            $this->assertStringContainsString('name="video_upload_id"', $body);
            $this->assertStringContainsString('vid-1', $body);
            $this->assertStringContainsString('name="part_content"; filename="' . basename($path) . '"', $body);

            $this->assertArrayNotHasKey('access_token', $this->lastRequestQuery());
        });
    }

    public function testCompleteVideoUploadUsesPublicLevelAuth(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        (new MediaSpace($client))->completeVideoUpload([
            'video_upload_id' => 'vid-1',
            'part_seq_list' => [0, 1],
            'report_data' => ['upload_cost' => 100],
        ]);

        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/media_space/complete_video_upload', $this->lastRequest()->getUri()->getPath());
        $this->assertArrayNotHasKey('access_token', $this->lastRequestQuery());
    }

    public function testGetVideoUploadResultUsesShopLevelAuth(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new MediaSpace($client))->getVideoUploadResult(['video_upload_id' => 'vid-1']);

        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/media_space/get_video_upload_result', $this->lastRequest()->getUri()->getPath());
        $this->assertSame('14701711', $this->lastRequestQuery()['shop_id']);
    }

    public function testCancelVideoUploadUsesShopLevelAuth(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new MediaSpace($client))->cancelVideoUpload(['video_upload_id' => 'vid-1']);

        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/media_space/cancel_video_upload', $this->lastRequest()->getUri()->getPath());
    }
}
