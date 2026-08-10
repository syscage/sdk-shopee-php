<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Auth;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Auth\OAuth;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class OAuthTest extends TestCase
{
    use InteractsWithMockHttp;

    public function testGetAuthorizationUrlBuildsFixedUrlWithParams(): void
    {
        $credentials = $this->testCredentials();
        $oauth = new OAuth($credentials, $this->makeClient([], $credentials));

        $url = $oauth->getAuthorizationUrl('https://example.com/callback', state: 'xyz');

        $this->assertStringStartsWith('https://open.test-stable.shopee.com/auth?', $url);

        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        $this->assertSame((string) $credentials->partnerId, $query['partner_id']);
        $this->assertSame('seller', $query['auth_type']);
        $this->assertSame('https://example.com/callback', $query['redirect_uri']);
        $this->assertSame('code', $query['response_type']);
        $this->assertSame('xyz', $query['state']);
    }

    public function testGetCancelAuthorizationUrlUsesCancelPath(): void
    {
        $credentials = $this->testCredentials();
        $oauth = new OAuth($credentials, $this->makeClient([], $credentials));

        $url = $oauth->getCancelAuthorizationUrl('https://example.com/callback');

        $this->assertStringStartsWith('https://open.test-stable.shopee.com/cancel_auth?', $url);
    }

    public function testGetAccessTokenForShopSendsPublicSignedRequest(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'error' => '',
                'message' => '',
                'access_token' => 'access-1',
                'refresh_token' => 'refresh-1',
                'expire_in' => 14400,
            ])),
        ]);
        $oauth = new OAuth($this->testCredentials(), $client);

        $token = $oauth->getAccessTokenForShop('the-code', 54804);

        $this->assertSame('access-1', $token->accessToken);
        $this->assertSame('refresh-1', $token->refreshToken);
        $this->assertSame(54804, $token->shopId);

        $this->assertSame('/api/v2/auth/token/get', $this->lastRequest()->getUri()->getPath());
        $this->assertSame('POST', $this->lastRequest()->getMethod());

        $query = $this->lastRequestQuery();
        $this->assertArrayNotHasKey('access_token', $query);

        $body = $this->lastRequestBodyAsArray();
        $this->assertSame('the-code', $body['code']);
        $this->assertSame(54804, $body['shop_id']);
    }

    public function testRefreshAccessTokenForMerchantSendsPublicSignedRequest(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode([
                'error' => '',
                'message' => '',
                'access_token' => 'access-2',
                'refresh_token' => 'refresh-2',
                'expire_in' => 14400,
            ])),
        ]);
        $oauth = new OAuth($this->testCredentials(), $client);

        $token = $oauth->refreshAccessTokenForMerchant('old-refresh', 1001705);

        $this->assertSame('access-2', $token->accessToken);
        $this->assertSame(1001705, $token->merchantId);
        $this->assertSame('/api/v2/auth/access_token/get', $this->lastRequest()->getUri()->getPath());

        $body = $this->lastRequestBodyAsArray();
        $this->assertSame('old-refresh', $body['refresh_token']);
        $this->assertSame(1001705, $body['merchant_id']);
    }
}
