<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Logistics;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Logistics\ServiceableArea;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ServiceableAreaTest extends TestCase
{
    use InteractsWithMockHttp;

    public function testUploadServiceablePolygonUsesThePathFromShopeesOwnExamplesNotItsDocHeader(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => '', 'task_id' => 'task-1'])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        $tmpFile = tempnam(sys_get_temp_dir(), 'shopee-sdk-test-');
        file_put_contents($tmpFile, '<kml>fake polygon</kml>');
        rename($tmpFile, $tmpFile . '.kml');
        $tmpFile .= '.kml';

        try {
            $response = (new ServiceableArea($client))->uploadServiceablePolygon($tmpFile);

            $this->assertSame('task-1', $response['task_id']);

            $request = $this->lastRequest();
            $this->assertSame('POST', $request->getMethod());
            // Shopee's doc header says upload_serviceable_polygon, but every
            // code example in that same doc calls upload_polygon — see
            // ServiceableArea::uploadServiceablePolygon() docblock.
            $this->assertSame('/api/v2/logistics/upload_polygon', $request->getUri()->getPath());
            $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));

            $body = (string) $request->getBody();
            $this->assertStringContainsString('name="file"; filename="' . basename($tmpFile) . '"', $body);
            $this->assertStringContainsString('<kml>fake polygon</kml>', $body);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testUploadServiceablePolygonThrowsOnUnreadableFile(): void
    {
        $client = $this->makeClient([])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 1));

        $this->expectException(\InvalidArgumentException::class);

        (new ServiceableArea($client))->uploadServiceablePolygon('/nonexistent/path/does-not-exist.kml');
    }

    public function testCheckPolygonUpdateStatusUsesExpectedHttpMethodAndPath(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new ServiceableArea($client))->checkPolygonUpdateStatus(['task_id' => 'task-1']);

        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/logistics/check_polygon_update_status', $this->lastRequest()->getUri()->getPath());
    }
}
