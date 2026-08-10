<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Media;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class MediaTest extends TestCase
{
    use InteractsWithMockHttp;

    public function testUploadImageSendsPublicMultipartRequest(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        $path = tempnam(sys_get_temp_dir(), 'shopee-sdk-test-');
        file_put_contents($path, 'fake image bytes');

        try {
            (new Media($client))->uploadImage([$path], business: 2, scene: 1);

            $request = $this->lastRequest();
            $this->assertSame('POST', $request->getMethod());
            $this->assertSame('/api/v2/media/upload_image', $request->getUri()->getPath());
            $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));

            $body = (string) $request->getBody();
            $this->assertStringContainsString('name="business"', $body);
            $this->assertStringContainsString('name="scene"', $body);
            $this->assertStringContainsString('name="images"; filename="' . basename($path) . '"', $body);

            $query = $this->lastRequestQuery();
            $this->assertArrayHasKey('partner_id', $query);
            $this->assertArrayNotHasKey('access_token', $query);
            $this->assertArrayNotHasKey('shop_id', $query);
        } finally {
            unlink($path);
        }
    }

    public function testUploadImageThrowsWhenNoFilesGiven(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Media($this->makeClient([])))->uploadImage([], business: 2, scene: 1);
    }

    public function testInitVideoUploadSignsAtPublicLevel(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        (new Media($client))->initVideoUpload([
            'business' => 3,
            'scene' => 1,
            'file_name' => 'video.mp4',
            'file_size' => 100,
            'duration' => 10,
        ]);

        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/media/init_video_upload', $this->lastRequest()->getUri()->getPath());
        $this->assertArrayNotHasKey('access_token', $this->lastRequestQuery());
    }

    public function testUploadVideoPartSendsPublicMultipartRequest(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        $path = tempnam(sys_get_temp_dir(), 'shopee-sdk-test-');
        file_put_contents($path, 'fake video part bytes');

        try {
            (new Media($client))->uploadVideoPart('vid-1', 0, 'md5hash', $path);

            $request = $this->lastRequest();
            $this->assertSame('POST', $request->getMethod());
            $this->assertSame('/api/v2/media/upload_video_part', $request->getUri()->getPath());

            $body = (string) $request->getBody();
            $this->assertStringContainsString('name="video_upload_id"', $body);
            $this->assertStringContainsString('vid-1', $body);
            $this->assertStringContainsString('name="part_content"; filename="' . basename($path) . '"', $body);

            $this->assertArrayNotHasKey('access_token', $this->lastRequestQuery());
        } finally {
            unlink($path);
        }
    }

    public function testCompleteVideoUploadSignsAtPublicLevel(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        (new Media($client))->completeVideoUpload(['video_upload_id' => 'vid-1']);

        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/media/complete_video_upload', $this->lastRequest()->getUri()->getPath());
        $this->assertArrayNotHasKey('access_token', $this->lastRequestQuery());
    }

    public function testGetVideoUploadResultSignsAtPublicLevel(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        (new Media($client))->getVideoUploadResult(['video_upload_id' => 'vid-1']);

        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/media/get_video_upload_result', $this->lastRequest()->getUri()->getPath());
        $this->assertArrayNotHasKey('access_token', $this->lastRequestQuery());
    }

    public function testCancelVideoUploadSignsAtPublicLevel(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        (new Media($client))->cancelVideoUpload(['video_upload_id' => 'vid-1']);

        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/media/cancel_video_upload', $this->lastRequest()->getUri()->getPath());
        $this->assertArrayNotHasKey('access_token', $this->lastRequestQuery());
    }
}
