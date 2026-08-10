<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Laravel;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;
use Syscage\Sdk\Shopee\Core\Http\Auth\Signer;
use Syscage\Sdk\Shopee\Core\Http\Client;
use Syscage\Sdk\Shopee\Core\Shopee;

/**
 * Wires the framework-independent SDK into a Laravel application.
 *
 * Binds {@see Credentials}, {@see Client}, and {@see Shopee} as container
 * singletons built from the published `config/shopee.php`. Contains no API
 * requests, signing, or authentication logic of its own — it only adapts
 * existing Core SDK classes, per CLAUDE.md and `.ai/prompts/frameworks/framework.md`.
 */
final class ShopeeServiceProvider extends ServiceProvider
{
    private const CONFIG_PATH = __DIR__ . '/config/shopee.php';

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'shopee');

        $this->app->singleton(Credentials::class, function (Application $app): Credentials {
            $config = $app['config']->get('shopee', []);

            return new Credentials(
                partnerId: (int) ($config['partner_id'] ?? 0),
                partnerKey: (string) ($config['partner_key'] ?? ''),
                apiUrl: (string) ($config['api_url'] ?? ''),
                authUrl: (string) ($config['auth_url'] ?? ''),
            );
        });

        $this->app->singleton(Client::class, function (Application $app): Client {
            $config = $app['config']->get('shopee', []);

            $httpClient = new GuzzleClient([
                'timeout' => (int) ($config['http']['timeout'] ?? 30),
                'connect_timeout' => (int) ($config['http']['connect_timeout'] ?? 10),
            ]);

            return new Client(
                credentials: $app->make(Credentials::class),
                httpClient: $httpClient,
                signer: new Signer((string) ($config['hash']['algorithm'] ?? 'sha256')),
            );
        });

        $this->app->singleton(Shopee::class, fn (Application $app): Shopee => new Shopee(
            credentials: $app->make(Credentials::class),
            client: $app->make(Client::class),
        ));

        $this->app->alias(Shopee::class, 'shopee');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG_PATH => $this->app->configPath('shopee.php'),
            ], 'shopee-config');
        }
    }

    /**
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            Credentials::class,
            Client::class,
            Shopee::class,
            'shopee',
        ];
    }
}
