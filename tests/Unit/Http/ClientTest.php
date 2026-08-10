<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Exceptions\ApiException;
use Syscage\Sdk\Shopee\Core\Exceptions\TransportException;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ClientTest extends TestCase
{
    use InteractsWithMockHttp;

    public function testPublicRequestSignsAndSendsCommonParamsInQuery(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => '', 'request_id' => 'r1'])),
        ]);

        $client->public('POST', '/api/v2/auth/token/get', ['partner_id' => 2001887, 'code' => 'abc', 'shop_id' => 1]);

        $query = $this->lastRequestQuery();
        $this->assertSame('2001887', $query['partner_id']);
        $this->assertArrayHasKey('timestamp', $query);
        $this->assertArrayHasKey('sign', $query);
        $this->assertArrayNotHasKey('access_token', $query);

        $body = $this->lastRequestBodyAsArray();
        $this->assertSame(['partner_id' => 2001887, 'code' => 'abc', 'shop_id' => 1], $body);
    }

    public function testShopGetRequestPutsRequestParamsInQuery(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => '', 'shop_name' => 'Acme'])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, shopId: 999));

        $response = $client->shop('GET', '/api/v2/shop/get_shop_info');

        $this->assertSame('Acme', $response['shop_name']);

        $query = $this->lastRequestQuery();
        $this->assertSame('token-123', $query['access_token']);
        $this->assertSame('999', $query['shop_id']);
        $this->assertSame('GET', $this->lastRequest()->getMethod());
    }

    public function testShopPostRequestPutsRequestParamsInBody(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => '', 'response' => []])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, shopId: 999));

        $client->shop('POST', '/api/v2/shop/update_profile', ['shop_name' => 'New Name']);

        $this->assertSame(['shop_name' => 'New Name'], $this->lastRequestBodyAsArray());

        $query = $this->lastRequestQuery();
        $this->assertArrayNotHasKey('shop_name', $query);
        $this->assertSame('999', $query['shop_id']);
    }

    public function testShopRequestWithoutAccessTokenThrows(): void
    {
        $client = $this->makeClient([]);

        $this->expectException(\LogicException::class);

        $client->shop('GET', '/api/v2/shop/get_shop_info');
    }

    public function testShopRequestWithoutShopIdThrows(): void
    {
        $client = $this->makeClient([])
            ->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600));

        $this->expectException(\InvalidArgumentException::class);

        $client->shop('GET', '/api/v2/shop/get_shop_info');
    }

    public function testPrincipalRequestPutsRequestParamsInBody(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => '', 'response' => []])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, principalId: 55001));

        $client->principal('POST', '/api/v2/principal/get_principal_livestream_performance', ['start_date' => '2026-01-01']);

        $this->assertSame(['start_date' => '2026-01-01'], $this->lastRequestBodyAsArray());

        $query = $this->lastRequestQuery();
        $this->assertSame('token-123', $query['access_token']);
        $this->assertSame('55001', $query['principal_id']);
        $this->assertArrayNotHasKey('shop_id', $query);
        $this->assertArrayNotHasKey('merchant_id', $query);
    }

    public function testPrincipalRequestWithoutPrincipalIdThrows(): void
    {
        $client = $this->makeClient([])
            ->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600));

        $this->expectException(\InvalidArgumentException::class);

        $client->principal('POST', '/api/v2/principal/get_principal_livestream_performance');
    }

    public function testUserRequestPutsRequestParamsInQuery(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => '', 'response' => []])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, userId: 987654));

        $client->user('GET', '/api/v2/video/get_video_list', ['page_no' => 1]);

        $query = $this->lastRequestQuery();
        $this->assertSame('token-123', $query['access_token']);
        $this->assertSame('987654', $query['user_id']);
        $this->assertSame('1', $query['page_no']);
        $this->assertArrayNotHasKey('shop_id', $query);
        $this->assertArrayNotHasKey('merchant_id', $query);
        $this->assertArrayNotHasKey('principal_id', $query);
    }

    public function testUserRequestWithoutUserIdThrows(): void
    {
        $client = $this->makeClient([])
            ->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600));

        $this->expectException(\InvalidArgumentException::class);

        $client->user('GET', '/api/v2/video/get_video_list');
    }

    public function testErrorResponseThrowsApiException(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'request_id' => 'req-1',
                'error' => 'error_auth',
                'message' => 'Invalid partner_id or shopid.',
            ])),
        ]);

        try {
            $client->public('GET', '/api/v2/some/path');
            $this->fail('Expected an ApiException.');
        } catch (ApiException $e) {
            $this->assertSame('error_auth', $e->getErrorCode());
            $this->assertSame('Invalid partner_id or shopid.', $e->getShopeeMessage());
            $this->assertSame('req-1', $e->getRequestId());
            $this->assertSame(200, $e->getHttpStatus());
        }
    }

    public function testNonJsonResponseThrowsTransportException(): void
    {
        $client = $this->makeClient([
            new Response(200, [], '<html>not json</html>'),
        ]);

        $this->expectException(TransportException::class);

        $client->public('GET', '/api/v2/some/path');
    }

    public function testShopUploadSendsMultipartRequestWithFieldsAndFile(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => '', 'request_id' => 'r1'])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, shopId: 999));

        $tmpFile = tempnam(sys_get_temp_dir(), 'shopee-sdk-test-');
        file_put_contents($tmpFile, '%PDF-1.4 fake content');

        try {
            $response = $client->shopUpload(
                '/api/v2/order/upload_invoice_doc',
                ['order_sn' => 'ORDER123', 'file_type' => 1],
                'file',
                fopen($tmpFile, 'rb'),
                'invoice.pdf',
            );

            $this->assertSame('', $response['error']);

            $request = $this->lastRequest();
            $this->assertSame('POST', $request->getMethod());
            $this->assertStringStartsWith('multipart/form-data; boundary=', $request->getHeaderLine('Content-Type'));

            $body = (string) $request->getBody();
            $this->assertStringContainsString('name="order_sn"', $body);
            $this->assertStringContainsString('ORDER123', $body);
            $this->assertStringContainsString('name="file"; filename="invoice.pdf"', $body);
            $this->assertStringContainsString('%PDF-1.4 fake content', $body);

            $query = $this->lastRequestQuery();
            $this->assertSame('999', $query['shop_id']);
            $this->assertSame('token-123', $query['access_token']);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testShopDownloadReturnsRawBodyOnSuccess(): void
    {
        $client = $this->makeClient([
            new Response(200, ['Content-Type' => 'application/pdf'], '%PDF-1.4 raw bytes'),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, shopId: 999));

        $bytes = $client->shopDownload('/api/v2/order/download_invoice_doc', ['order_sn' => 'ORDER123']);

        $this->assertSame('%PDF-1.4 raw bytes', $bytes);
        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('ORDER123', $this->lastRequestQuery()['order_sn']);
    }

    public function testShopDownloadThrowsApiExceptionOnJsonErrorBody(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'request_id' => 'req-2',
                'error' => 'order.download_invoice_error',
                'message' => 'Download invoice failed.',
            ])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, shopId: 999));

        $this->expectException(ApiException::class);

        $client->shopDownload('/api/v2/order/download_invoice_doc', ['order_sn' => 'ORDER123']);
    }

    public function testShopDownloadPostSendsJsonBodyAndReturnsRawBodyOnSuccess(): void
    {
        $client = $this->makeClient([
            new Response(200, ['Content-Type' => 'application/pdf'], '%PDF-1.4 raw bytes'),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, shopId: 999));

        $bytes = $client->shopDownloadPost('/api/v2/logistics/download_shipping_document', [
            'order_list' => [['order_sn' => 'ORDER123']],
        ]);

        $this->assertSame('%PDF-1.4 raw bytes', $bytes);
        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame(
            ['order_list' => [['order_sn' => 'ORDER123']]],
            $this->lastRequestBodyAsArray(),
        );
        $this->assertSame('999', $this->lastRequestQuery()['shop_id']);
    }

    public function testShopDownloadPostThrowsApiExceptionOnJsonErrorBody(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'request_id' => 'req-3',
                'error' => 'error_param',
                'message' => 'Document not ready.',
            ])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, shopId: 999));

        $this->expectException(ApiException::class);

        $client->shopDownloadPost('/api/v2/logistics/download_shipping_document', [
            'order_list' => [['order_sn' => 'ORDER123']],
        ]);
    }

    public function testUserUploadSendsMultipartRequestWithUserLevelSigning(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => '', 'request_id' => 'r1'])),
        ])->withAccessToken(new AccessToken('token-123', 'refresh-123', time() + 3600, userId: 987654));

        $response = $client->userUpload(
            '/api/v2/livestream/upload_image',
            [],
            'image',
            'fake image bytes',
            'cover.jpg',
        );

        $this->assertSame('', $response['error']);

        $request = $this->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));

        $body = (string) $request->getBody();
        $this->assertStringContainsString('name="image"; filename="cover.jpg"', $body);

        $query = $this->lastRequestQuery();
        $this->assertSame('token-123', $query['access_token']);
        $this->assertSame('987654', $query['user_id']);
        $this->assertArrayNotHasKey('shop_id', $query);
    }

    public function testPublicUploadSendsMultipartRequestWithoutAccessToken(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => '', 'request_id' => 'r1'])),
        ]);

        $response = $client->publicUpload(
            '/api/v2/media_space/upload_image',
            ['scene' => 'normal'],
            [['name' => 'image', 'contents' => 'fake image bytes', 'filename' => 'photo.jpg']],
        );

        $this->assertSame('', $response['error']);

        $request = $this->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));

        $body = (string) $request->getBody();
        $this->assertStringContainsString('name="scene"', $body);
        $this->assertStringContainsString('name="image"; filename="photo.jpg"', $body);
        $this->assertStringContainsString('fake image bytes', $body);

        $query = $this->lastRequestQuery();
        $this->assertArrayHasKey('partner_id', $query);
        $this->assertArrayNotHasKey('access_token', $query);
        $this->assertArrayNotHasKey('shop_id', $query);
    }

    public function testPublicUploadSupportsMultipleFileParts(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        $client->publicUpload('/api/v2/media_space/upload_image', [], [
            ['name' => 'image', 'contents' => 'first image', 'filename' => 'a.jpg'],
            ['name' => 'image', 'contents' => 'second image', 'filename' => 'b.jpg'],
        ]);

        $body = (string) $this->lastRequest()->getBody();
        $this->assertStringContainsString('filename="a.jpg"', $body);
        $this->assertStringContainsString('filename="b.jpg"', $body);
    }
}
