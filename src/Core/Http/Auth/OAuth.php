<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Auth;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Shopee Open Platform authorization flow: generating the authorization link,
 * exchanging the authorization code for tokens, and refreshing tokens.
 *
 * @see .shopee-docs/Developer Guide/Getting Started/authorization_and_authentication.md
 */
final class OAuth
{
    public function __construct(
        private readonly Credentials $credentials,
        private readonly Client $client,
    ) {
    }

    /**
     * Build the link sellers use to grant your app authorization.
     *
     * @param string $authType One of "seller", "supplier", "user".
     */
    public function getAuthorizationUrl(string $redirectUri, string $authType = 'seller', ?string $state = null): string
    {
        return $this->buildAuthUrl('/auth', $redirectUri, $authType, $state);
    }

    /**
     * Build the link used to revoke a previously granted authorization.
     *
     * @param string $authType One of "seller", "supplier", "user".
     */
    public function getCancelAuthorizationUrl(string $redirectUri, string $authType = 'seller', ?string $state = null): string
    {
        return $this->buildAuthUrl('/cancel_auth', $redirectUri, $authType, $state);
    }

    /**
     * Exchange an authorization `code` for the first access/refresh token pair
     * of a single shop.
     */
    public function getAccessTokenForShop(string $code, int $shopId): AccessToken
    {
        $response = $this->client->public('POST', '/api/v2/auth/token/get', [
            'partner_id' => $this->credentials->partnerId,
            'code' => $code,
            'shop_id' => $shopId,
        ]);

        return AccessToken::fromResponse($response, shopId: $shopId);
    }

    /**
     * Exchange an authorization `code` for the first access/refresh token pair
     * of a main account. The response lists every shop_id / merchant_id that
     * was authorized; call {@see refreshAccessTokenForShop()} or
     * {@see refreshAccessTokenForMerchant()} to obtain each one's own token pair.
     *
     * @return array<string, mixed> Decoded GetAccessToken response.
     */
    public function getAccessTokenForMainAccount(string $code, int $mainAccountId): array
    {
        return $this->client->public('POST', '/api/v2/auth/token/get', [
            'partner_id' => $this->credentials->partnerId,
            'code' => $code,
            'main_account_id' => $mainAccountId,
        ]);
    }

    /**
     * Refresh a shop's access token before it expires.
     */
    public function refreshAccessTokenForShop(string $refreshToken, int $shopId): AccessToken
    {
        $response = $this->client->public('POST', '/api/v2/auth/access_token/get', [
            'partner_id' => $this->credentials->partnerId,
            'refresh_token' => $refreshToken,
            'shop_id' => $shopId,
        ]);

        return AccessToken::fromResponse($response, shopId: $shopId);
    }

    /**
     * Refresh a merchant's access token before it expires.
     */
    public function refreshAccessTokenForMerchant(string $refreshToken, int $merchantId): AccessToken
    {
        $response = $this->client->public('POST', '/api/v2/auth/access_token/get', [
            'partner_id' => $this->credentials->partnerId,
            'refresh_token' => $refreshToken,
            'merchant_id' => $merchantId,
        ]);

        return AccessToken::fromResponse($response, merchantId: $merchantId);
    }

    private function buildAuthUrl(string $path, string $redirectUri, string $authType, ?string $state): string
    {
        $query = [
            'partner_id' => $this->credentials->partnerId,
            'auth_type' => $authType,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
        ];

        if ($state !== null) {
            $query['state'] = $state;
        }

        return $this->credentials->authUrl . $path . '?' . http_build_query($query);
    }
}
