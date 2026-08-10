<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Framework\Symfony;

use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Symfony\DependencyInjection\ShopeeExtension;
use Syscage\Sdk\Shopee\Symfony\ShopeeBundle;

final class ShopeeBundleTest extends TestCase
{
    public function testResolvesItsContainerExtensionByDefaultNamingConvention(): void
    {
        $extension = (new ShopeeBundle())->getContainerExtension();

        $this->assertInstanceOf(ShopeeExtension::class, $extension);
    }
}
