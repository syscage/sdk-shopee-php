<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Symfony\DependencyInjection;

use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;
use Syscage\Sdk\Shopee\Core\Http\Auth\Signer;
use Syscage\Sdk\Shopee\Core\Http\Client;
use Syscage\Sdk\Shopee\Core\Shopee;

/**
 * Registers the Core SDK's services in the Symfony container, built from
 * the `shopee` configuration tree ({@see Configuration}).
 *
 * Contains no API requests, signing, or authentication logic of its own —
 * it only adapts existing Core SDK classes, per CLAUDE.md and
 * `.ai/prompts/frameworks/framework.md`. Every registered service is
 * public, since this integration provides no Facade (per
 * `.ai/prompts/frameworks/implement-symfony.md` §7) — direct container
 * access is the supported way to reach these services outside of autowiring.
 */
final class ShopeeExtension extends Extension
{
    /**
     * @param array<int, array<string, mixed>> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->register(Credentials::class, Credentials::class)
            ->setArguments([
                $config['partner_id'],
                $config['partner_key'],
                $config['api_url'],
                $config['auth_url'],
            ])
            ->setPublic(true);

        $container->register(Signer::class, Signer::class)
            ->setArguments([$config['hash']['algorithm']])
            ->setPublic(true);

        $container->register('shopee.http_client', GuzzleClient::class)
            ->setArguments([[
                'timeout' => $config['http']['timeout'],
                'connect_timeout' => $config['http']['connect_timeout'],
            ]])
            ->setPublic(true);

        $container->register(Client::class, Client::class)
            ->setArguments([
                new Reference(Credentials::class),
                null,
                new Reference('shopee.http_client'),
                null,
                null,
                new Reference(Signer::class),
            ])
            ->setPublic(true);

        $container->register(Shopee::class, Shopee::class)
            ->setArguments([
                new Reference(Credentials::class),
                null,
                new Reference(Client::class),
            ])
            ->setPublic(true);

        $container->setAlias('shopee', Shopee::class)->setPublic(true);
    }
}
