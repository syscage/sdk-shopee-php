<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Defines the `shopee` configuration tree, e.g. `config/packages/shopee.yaml`:
 *
 * ```yaml
 * shopee:
 *     partner_id: '%env(int:SHOPEE_PARTNER_ID)%'
 *     partner_key: '%env(SHOPEE_PARTNER_KEY)%'
 * ```
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('shopee');

        $treeBuilder->getRootNode()
            ->children()
                ->integerNode('partner_id')->isRequired()->end()
                ->scalarNode('partner_key')->isRequired()->end()
                ->scalarNode('api_url')->defaultValue('https://partner.shopeemobile.com')->end()
                ->scalarNode('auth_url')->defaultValue('https://open.shopee.com')->end()
                ->arrayNode('hash')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('algorithm')->defaultValue('sha256')->end()
                    ->end()
                ->end()
                ->arrayNode('http')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('timeout')->defaultValue(30)->end()
                        ->integerNode('connect_timeout')->defaultValue(10)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
