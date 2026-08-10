<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Auth;

use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;

final class AccessTokenTest extends TestCase
{
    public function testFromResponseUsesGivenShopIdOverResponse(): void
    {
        $token = AccessToken::fromResponse([
            'access_token' => 'abc',
            'refresh_token' => 'def',
            'expire_in' => 14400,
        ], shopId: 54804);

        $this->assertSame('abc', $token->accessToken);
        $this->assertSame('def', $token->refreshToken);
        $this->assertSame(54804, $token->shopId);
        $this->assertNull($token->merchantId);
        $this->assertNull($token->principalId);
        $this->assertFalse($token->isExpired());
    }

    public function testFromResponseFallsBackToPrincipalIdInResponse(): void
    {
        $token = AccessToken::fromResponse([
            'access_token' => 'abc',
            'refresh_token' => 'def',
            'expire_in' => 14400,
            'principal_id' => 55001,
        ]);

        $this->assertSame(55001, $token->principalId);
    }

    public function testFromResponseFallsBackToUserIdInResponse(): void
    {
        $token = AccessToken::fromResponse([
            'access_token' => 'abc',
            'refresh_token' => 'def',
            'expire_in' => 14400,
            'user_id' => 987654,
        ]);

        $this->assertSame(987654, $token->userId);
    }

    public function testFromResponseFallsBackToShopIdInResponse(): void
    {
        $token = AccessToken::fromResponse([
            'access_token' => 'abc',
            'refresh_token' => 'def',
            'expire_in' => 14400,
            'shop_id' => 33142,
        ]);

        $this->assertSame(33142, $token->shopId);
    }

    public function testIsExpiredHonoursBuffer(): void
    {
        $token = new AccessToken('abc', 'def', expiresAt: time() + 30);

        $this->assertFalse($token->isExpired(bufferSeconds: 0));
        $this->assertTrue($token->isExpired(bufferSeconds: 60));
    }
}
