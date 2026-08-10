<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Symfony;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Registers the framework-independent Core SDK's services into a Symfony
 * application's container.
 *
 * The container extension ({@see DependencyInjection\ShopeeExtension}) is
 * auto-discovered by Symfony's default {@see Bundle::getContainerExtension()}
 * naming convention (`{bundle namespace}\DependencyInjection\{Name}Extension`) —
 * no override needed here.
 */
final class ShopeeBundle extends Bundle
{
}
