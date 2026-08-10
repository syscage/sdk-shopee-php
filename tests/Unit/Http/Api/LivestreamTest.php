<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Livestream;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class LivestreamTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'createSession' => ['createSession', 'POST', '/api/v2/livestream/create_session', ['title' => 'Big Sale', 'cover_image_url' => 'https://cf.shopee.test/cover.jpg']],
            'startSession' => ['startSession', 'POST', '/api/v2/livestream/start_session', ['session_id' => 1, 'domain_id' => 1]],
            'updateSession' => ['updateSession', 'POST', '/api/v2/livestream/update_session', ['session_id' => 1, 'title' => 'Big Sale', 'cover_image_url' => 'https://cf.shopee.test/cover.jpg', 'is_test' => false]],
            'endSession' => ['endSession', 'POST', '/api/v2/livestream/end_session', ['session_id' => 1]],
            'getSessionDetail' => ['getSessionDetail', 'GET', '/api/v2/livestream/get_session_detail', ['session_id' => 1]],
            'getSessionMetric' => ['getSessionMetric', 'GET', '/api/v2/livestream/get_session_metric', ['session_id' => 1]],
            'getSessionItemMetric' => ['getSessionItemMetric', 'GET', '/api/v2/livestream/get_session_item_metric', ['session_id' => 1, 'offset' => 0, 'page_size' => 20]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, userId: 987654));

        (new Livestream($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testUploadImageSendsUserLevelMultipartRequest(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, userId: 987654));

        $path = tempnam(sys_get_temp_dir(), 'shopee-sdk-test-');
        file_put_contents($path, 'fake image bytes');

        try {
            (new Livestream($client))->uploadImage($path);

            $request = $this->lastRequest();
            $this->assertSame('POST', $request->getMethod());
            $this->assertSame('/api/v2/livestream/upload_image', $request->getUri()->getPath());
            $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));

            $body = (string) $request->getBody();
            $this->assertStringContainsString('name="image"; filename="' . basename($path) . '"', $body);

            $query = $this->lastRequestQuery();
            $this->assertSame('987654', $query['user_id']);
            $this->assertArrayNotHasKey('shop_id', $query);
        } finally {
            unlink($path);
        }
    }

    public function testUploadImageThrowsWhenFileUnreadable(): void
    {
        $client = $this->makeClient([])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, userId: 987654));

        $this->expectException(\InvalidArgumentException::class);

        (new Livestream($client))->uploadImage('/path/does/not/exist.jpg');
    }

    public function testSubAccessorsAreCached(): void
    {
        $livestream = new Livestream($this->makeClient([]));

        $this->assertSame($livestream->item(), $livestream->item());
        $this->assertSame($livestream->showItem(), $livestream->showItem());
        $this->assertSame($livestream->itemSet(), $livestream->itemSet());
        $this->assertSame($livestream->comment(), $livestream->comment());
    }
}
