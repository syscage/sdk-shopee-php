<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Framework\Symfony;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;
use Syscage\Sdk\Shopee\Core\Http\Client;
use Syscage\Sdk\Shopee\Core\Shopee;
use Syscage\Sdk\Shopee\Symfony\DependencyInjection\ShopeeExtension;

final class ShopeeExtensionTest extends TestCase
{
    /**
     * @param array<string, mixed> $config
     */
    private function buildContainer(array $config = []): ContainerBuilder
    {
        $container = new ContainerBuilder();
        (new ShopeeExtension())->load([[
            'partner_id' => 2001887,
            'partner_key' => 'test-partner-key',
            ...$config,
        ]], $container);
        $container->compile();

        return $container;
    }

    public function testExtensionAliasMatchesConfigurationRootKey(): void
    {
        $this->assertSame('shopee', (new ShopeeExtension())->getAlias());
    }

    public function testAppliesConfigurationDefaultsWhenNotOverridden(): void
    {
        $credentials = $this->buildContainer()->get(Credentials::class);

        $this->assertSame('https://partner.shopeemobile.com', $credentials->apiUrl);
        $this->assertSame('https://open.shopee.com', $credentials->authUrl);
    }

    public function testBindsCredentialsFromConfig(): void
    {
        $credentials = $this->buildContainer([
            'api_url' => 'https://openplatform.sandbox.test-stable.shopee.sg',
            'auth_url' => 'https://open.test-stable.shopee.com',
        ])->get(Credentials::class);

        $this->assertInstanceOf(Credentials::class, $credentials);
        $this->assertSame(2001887, $credentials->partnerId);
        $this->assertSame('test-partner-key', $credentials->partnerKey);
        $this->assertSame('https://openplatform.sandbox.test-stable.shopee.sg', $credentials->apiUrl);
        $this->assertSame('https://open.test-stable.shopee.com', $credentials->authUrl);
    }

    public function testBindsClientAndShopeeSingletonsWithPublicAlias(): void
    {
        $container = $this->buildContainer();

        $client = $container->get(Client::class);
        $shopee = $container->get(Shopee::class);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(Shopee::class, $shopee);
        $this->assertSame($shopee, $container->get('shopee'));
        $this->assertSame($shopee, $container->get(Shopee::class));
        $this->assertSame($client, $container->get(Client::class));
    }

    public function testMissingRequiredPartnerIdThrows(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new ShopeeExtension())->load([['partner_key' => 'test-partner-key']], new ContainerBuilder());
    }

    public function testMissingRequiredPartnerKeyThrows(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        (new ShopeeExtension())->load([['partner_id' => 2001887]], new ContainerBuilder());
    }
}
