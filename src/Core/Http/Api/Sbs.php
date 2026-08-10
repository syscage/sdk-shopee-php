<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Sbs API domain (`v2.sbs.*`) — Seller-center warehouse reporting: bound
 * warehouses, current inventory, stock movement, stock aging, and expiry
 * status. Every endpoint is a read-only GET query against a warehouse
 * region (`whs_region`), except {@see getBoundWhsInfo()} which just
 * resolves the region(s) bound to the shop.
 *
 * Every endpoint signs at the **Shop** level.
 *
 * **Doc discrepancy**: `get_bound_whs_info.md`'s own Request Example section
 * shows a completely different path, `/api/v2/fbs/shop/get_bound_whs_info`
 * — but that doesn't match the doc's own header (`/api/v2/sbs/get_bound_whs_info`),
 * its own API Name (`v2.sbs.get_bound_whs_info`), nor Fbs's actual path
 * pattern (Fbs has no `/shop/` segment; see {@see Fbs}). No sibling Sbs
 * endpoint even has a Request Example section. Unlike prior doc/example
 * path mismatches (always a verb variant within the same domain — see
 * `.ai/state/current-task.md`), this one crosses to an unrelated domain
 * segment that fits nowhere, so it reads as a stale copy-paste rather than
 * the real path. Implemented using the header/API-Name path instead of the
 * example, the opposite call from previous mismatches — worth confirming
 * against the sandbox if `error_param`/routing errors occur.
 *
 * @see .shopee-docs/API Reference/Sbs
 * @see .docs/api/Sbs
 */
final class Sbs
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params (none required)
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Sbs/get_bound_whs_info.md
     */
    public function getBoundWhsInfo(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/sbs/get_bound_whs_info', $params);
    }

    /**
     * @param array<string, mixed> $params whs_region (required); page_no,
     *     page_size, search_type, keyword, whs_ids, not_moving_tag,
     *     inbound_pending_approval, products_with_inventory, category_id,
     *     stock_levels (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Sbs/get_current_inventory.md
     */
    public function getCurrentInventory(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/sbs/get_current_inventory', $params);
    }

    /**
     * @param array<string, mixed> $params start_time, end_time, whs_region
     *     (all required, dates YYYY-MM-DD, range must not exceed 90 days);
     *     page_no, page_size, whs_ids, category_id_l1, sku_id, item_id,
     *     item_name, variation (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Sbs/get_stock_movement.md
     */
    public function getStockMovement(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/sbs/get_stock_movement', $params);
    }

    /**
     * @param array<string, mixed> $params whs_region (required); page_no,
     *     page_size, search_type, keyword, whs_ids, aging_storage_tag,
     *     excess_storage_tag, category_id (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Sbs/get_stock_aging.md
     */
    public function getStockAging(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/sbs/get_stock_aging', $params);
    }

    /**
     * @param array<string, mixed> $params whs_region (required); page_no,
     *     page_size, whs_ids, expiry_status, category_id_l1, sku_id,
     *     item_id, variation, item_name (all optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Sbs/get_expiry_report.md
     */
    public function getExpiryReport(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/sbs/get_expiry_report', $params);
    }
}
