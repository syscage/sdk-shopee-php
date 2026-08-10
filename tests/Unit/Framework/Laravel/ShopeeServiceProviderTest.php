<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Framework\Laravel;

use Orchestra\Testbench\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;
use Syscage\Sdk\Shopee\Core\Http\Client;
use Syscage\Sdk\Shopee\Core\Shopee;
use Syscage\Sdk\Shopee\Laravel\Facades\Shopee as ShopeeFacade;
use Syscage\Sdk\Shopee\Laravel\ShopeeServiceProvider;

final class ShopeeServiceProviderTest extends TestCase
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [ShopeeServiceProvider::class];
    }

    /**
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return ['Shopee' => ShopeeFacade::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('shopee.partner_id', 2001887);
        $app['config']->set('shopee.partner_key', 'test-partner-key');
        $app['config']->set('shopee.api_url', 'https://openplatform.sandbox.test-stable.shopee.sg');
        $app['config']->set('shopee.auth_url', 'https://open.test-stable.shopee.com');
    }

    public function testMergesDefaultConfigWhenNotOverridden(): void
    {
        $this->assertSame('sha256', config('shopee.hash.algorithm'));
        $this->assertSame(30, config('shopee.http.timeout'));
        $this->assertSame(10, config('shopee.http.connect_timeout'));
    }

    public function testBindsCredentialsSingletonFromConfig(): void
    {
        $credentials = $this->app->make(Credentials::class);

        $this->assertInstanceOf(Credentials::class, $credentials);
        $this->assertSame(2001887, $credentials->partnerId);
        $this->assertSame('test-partner-key', $credentials->partnerKey);
        $this->assertSame('https://openplatform.sandbox.test-stable.shopee.sg', $credentials->apiUrl);
        $this->assertSame('https://open.test-stable.shopee.com', $credentials->authUrl);
        $this->assertSame($credentials, $this->app->make(Credentials::class));
    }

    public function testBindsClientSingleton(): void
    {
        $client = $this->app->make(Client::class);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertSame($client, $this->app->make(Client::class));
    }

    public function testBindsShopeeSingletonAndAlias(): void
    {
        $shopee = $this->app->make(Shopee::class);

        $this->assertInstanceOf(Shopee::class, $shopee);
        $this->assertSame($shopee, $this->app->make(Shopee::class));
        $this->assertSame($shopee, $this->app->make('shopee'));
    }

    public function testFacadeResolvesTheBoundShopeeInstance(): void
    {
        $shopee = $this->app->make(Shopee::class);

        $this->assertSame($shopee, ShopeeFacade::getFacadeRoot());
        $this->assertSame($shopee->oauth(), ShopeeFacade::oauth());
    }

    public function testPublishesConfigFile(): void
    {
        $paths = ShopeeServiceProvider::pathsToPublish(ShopeeServiceProvider::class, 'shopee-config');

        $this->assertCount(1, $paths);
        $this->assertStringEndsWith('config/shopee.php', array_key_first($paths));
        $this->assertStringEndsWith('shopee.php', array_values($paths)[0]);
        $this->assertFileExists(array_key_first($paths));
    }

    public function testProvidesListsContainerBindings(): void
    {
        $provider = new ShopeeServiceProvider($this->app);

        $this->assertSame(
            [Credentials::class, Client::class, Shopee::class, 'shopee'],
            $provider->provides(),
        );
    }
}
