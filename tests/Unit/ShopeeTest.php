<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;
use Syscage\Sdk\Shopee\Core\Shopee;

final class ShopeeTest extends TestCase
{
    private function credentials(): Credentials
    {
        return new Credentials(1, 'key', 'https://api.example.com', 'https://auth.example.com');
    }

    public function testWithAccessTokenReturnsNewScopedInstance(): void
    {
        $shopee = new Shopee($this->credentials());
        $token = new AccessToken('access', 'refresh', time() + 3600, shopId: 1);

        $scoped = $shopee->withAccessToken($token);

        $this->assertNull($shopee->getAccessToken());
        $this->assertSame($token, $scoped->getAccessToken());
    }

    public function testDomainAccessorsAreLazilyCachedPerInstance(): void
    {
        $shopee = new Shopee($this->credentials());

        $this->assertSame($shopee->oauth(), $shopee->oauth());
        $this->assertSame($shopee->shop(), $shopee->shop());
    }
}
