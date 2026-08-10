<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Shop API domain.
 *
 * @see .shopee-docs/API Reference/Shop
 * @see .docs/api/Shop
 */
final class Shop
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Shop/get_shop_info.md
     * @return array<string, mixed>
     */
    public function getShopInfo(): array
    {
        return $this->client->shop('GET', '/api/v2/shop/get_shop_info');
    }

    /**
     * @see .shopee-docs/API Reference/Shop/update_profile.md
     * @param array<string, mixed> $params shop_name, shop_logo, description — all optional.
     * @return array<string, mixed>
     */
    public function updateProfile(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop/update_profile', $params);
    }
}
