<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Video;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class VideoTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getVideoList' => ['getVideoList', 'GET', '/api/v2/video/get_video_list', ['page_no' => 1, 'page_size' => 20, 'list_type' => 2]],
            'getVideoDetail' => ['getVideoDetail', 'GET', '/api/v2/video/get_video_detail', ['post_id' => 'p1']],
            'getCoverList' => ['getCoverList', 'GET', '/api/v2/video/get_cover_list', ['video_upload_id' => 'v1']],
            'editVideoInfo' => ['editVideoInfo', 'POST', '/api/v2/video/edit_video_info', ['video_upload_list' => [['video_upload_id' => 'v1']]]],
            'postVideo' => ['postVideo', 'POST', '/api/v2/video/post_video', ['video_upload_id_list' => ['v1']]],
            'deleteVideo' => ['deleteVideo', 'POST', '/api/v2/video/delete_video', ['post_id_list' => ['p1']]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, userId: 987654));

        (new Video($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testCallsSignAsUserLevelNotShopLevel(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, userId: 987654));

        (new Video($client))->getVideoDetail(['post_id' => 'p1']);

        $query = $this->lastRequestQuery();
        $this->assertSame('987654', $query['user_id']);
        $this->assertSame('token-123', $query['access_token']);
        $this->assertArrayNotHasKey('shop_id', $query);
    }

    public function testAnalyticsAccessorIsCached(): void
    {
        $video = new Video($this->makeClient([]));

        $this->assertSame($video->analytics(), $video->analytics());
    }
}
