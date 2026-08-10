<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;
use Syscage\Sdk\Shopee\Core\Shopee;
use Syscage\Sdk\Shopee\Tests\Integration\Support\SandboxEnv;

/**
 * Exercises the SDK against Shopee's real sandbox environment using the
 * credentials in the project root `.env` (Claude Code testing only — see
 * CLAUDE.md §26/§27). Skipped entirely when `.env` or a required value is
 * absent. Never run as part of the default `unit` test suite
 * (`vendor/bin/phpunit --testsuite unit`); run explicitly via
 * `vendor/bin/phpunit --testsuite integration`.
 */
final class ShopSandboxTest extends TestCase
{
    public function testRefreshAccessTokenThenGetShopInfo(): void
    {
        $env = SandboxEnv::loadOrSkip($this);

        $credentials = new Credentials(
            partnerId: $env->partnerId,
            partnerKey: $env->partnerKey,
            apiUrl: $env->apiUrl,
            authUrl: $env->authUrl,
        );

        $shopee = new Shopee($credentials, $env->accessTokenObject());

        // The access token in .env is short-lived (4 hours); refresh it
        // first so this test doesn't depend on how recently it was minted.
        $refreshed = $shopee->oauth()->refreshAccessTokenForShop($env->refreshToken, $env->shopId);

        $this->assertNotSame('', $refreshed->accessToken);
        $this->assertNotSame('', $refreshed->refreshToken);
        $this->assertFalse($refreshed->isExpired());
        $this->assertSame($env->shopId, $refreshed->shopId);

        // Shopee's refresh_token is single-use — persist the new pair so
        // the next local run doesn't start from a dead refresh_token.
        $env->persistRefreshedToken($refreshed);

        $shopee = $shopee->withAccessToken($refreshed);
        $info = $shopee->shop()->getShopInfo();

        $this->assertArrayHasKey('shop_name', $info);
        $this->assertIsString($info['shop_name']);
    }
}
