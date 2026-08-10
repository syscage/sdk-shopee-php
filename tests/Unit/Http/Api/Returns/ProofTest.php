<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Returns;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Returns\Proof;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ProofTest extends TestCase
{
    use InteractsWithMockHttp;

    public function testQueryProofUsesShopLevelGet(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Proof($client))->queryProof(['return_sn' => '2504060QHMFPXW']);

        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/returns/query_proof', $this->lastRequest()->getUri()->getPath());
    }

    public function testUploadProofSendsJsonBody(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Proof($client))->uploadProof([
            'return_sn' => '2504060QHMFPXW',
            'photo' => [['url' => 'https://cf.shopee.test/a.jpg', 'thumbnail' => 'https://cf.shopee.test/a_thumb.jpg']],
        ]);

        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/returns/upload_proof', $this->lastRequest()->getUri()->getPath());
        $this->assertSame(
            ['return_sn' => '2504060QHMFPXW', 'photo' => [['url' => 'https://cf.shopee.test/a.jpg', 'thumbnail' => 'https://cf.shopee.test/a_thumb.jpg']]],
            $this->lastRequestBodyAsArray(),
        );
    }

    public function testConvertImageSendsMultipartRequestWithFile(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        $path = tempnam(sys_get_temp_dir(), 'shopee-sdk-test-');
        file_put_contents($path, 'fake image bytes');

        try {
            (new Proof($client))->convertImage('2504060QHMFPXW', $path);

            $request = $this->lastRequest();
            $this->assertSame('POST', $request->getMethod());
            $this->assertSame('/api/v2/returns/convert_image', $request->getUri()->getPath());
            $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));

            $body = (string) $request->getBody();
            $this->assertStringContainsString('name="return_sn"', $body);
            $this->assertStringContainsString('2504060QHMFPXW', $body);
            $this->assertStringContainsString('name="upload_image"; filename="' . basename($path) . '"', $body);

            $this->assertSame('14701711', $this->lastRequestQuery()['shop_id']);
        } finally {
            unlink($path);
        }
    }

    public function testConvertImageThrowsWhenFileUnreadable(): void
    {
        $client = $this->makeClient([])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        $this->expectException(\InvalidArgumentException::class);

        (new Proof($client))->convertImage('2504060QHMFPXW', '/path/does/not/exist.jpg');
    }
}
