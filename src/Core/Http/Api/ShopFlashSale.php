<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\ShopFlashSale\Item;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * ShopFlashSale API domain (`v2.shop_flash_sale.*`) — a shop's own
 * time-boxed flash sale sessions. Core lifecycle only; item management is
 * split into {@see Item} (accessed via {@see item()}) because it has its
 * own key space (`flash_sale_id` + `item_id` + optional `model_id`), its
 * own status enum, and its own pagination — a meaningfully different shape
 * from this class's flat entity CRUD.
 *
 * Typical flow: {@see getTimeSlotId()} for an available slot (`start_time`
 * must be in the future) → {@see createShopFlashSale()} → populate via
 * `item()->addItems()` → {@see updateShopFlashSale()} to enable/disable.
 * A flash sale has two independent state fields: `status` (0 deleted, 1
 * enabled, 2 disabled, 3 system_rejected — the entity's own lifecycle) and
 * `type` (1 upcoming, 2 ongoing, 3 expired — a read-only, time-derived
 * value). Deleting only works while `type` is upcoming; disabling an
 * enabled flash sale disables all of its items.
 *
 * Every endpoint signs at the **Shop** level.
 *
 * @see .shopee-docs/API Reference/ShopFlashSale
 * @see .docs/api/ShopFlashSale
 */
final class ShopFlashSale
{
    private ?Item $item = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function item(): Item
    {
        return $this->item ??= new Item($this->client);
    }

    /**
     * @param array<string, mixed> $params start_time, end_time (both
     *     required timestamps). Only slots with start_time in the future
     *     can be used with {@see createShopFlashSale()}.
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/get_time_slot_id.md
     */
    public function getTimeSlotId(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/shop_flash_sale/get_time_slot_id', $params);
    }

    /**
     * @param array<string, mixed> $params timeslot_id (required, from {@see getTimeSlotId()}).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/create_shop_flash_sale.md
     */
    public function createShopFlashSale(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_flash_sale/create_shop_flash_sale', $params);
    }

    /**
     * @param array<string, mixed> $params flash_sale_id (required); status
     *     (required, 1 = enable, 2 = disable — cannot edit a flash sale
     *     already in status 3 = system_rejected).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/update_shop_flash_sale.md
     */
    public function updateShopFlashSale(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_flash_sale/update_shop_flash_sale', $params);
    }

    /**
     * Only an upcoming flash sale (`type` = 1) can be deleted.
     *
     * @param array<string, mixed> $params flash_sale_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/delete_shop_flash_sale.md
     */
    public function deleteShopFlashSale(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/shop_flash_sale/delete_shop_flash_sale', $params);
    }

    /**
     * @param array<string, mixed> $params flash_sale_id (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/get_shop_flash_sale.md
     */
    public function getShopFlashSale(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/shop_flash_sale/get_shop_flash_sale', $params);
    }

    /**
     * @param array<string, mixed> $params type (required, 0 = all, 1 =
     *     upcoming, 2 = ongoing, 3 = expired), offset, limit (both
     *     required); start_time, end_time (optional, must be used together).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/ShopFlashSale/get_shop_flash_sale_list.md
     */
    public function getShopFlashSaleList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/shop_flash_sale/get_shop_flash_sale_list', $params);
    }
}
