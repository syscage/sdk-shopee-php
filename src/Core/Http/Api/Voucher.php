<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Voucher API domain — shop-level or product-level discount codes buyers
 * redeem at checkout. Unlike Discount/BundleDeal/AddOnDeal, applicable
 * items are a plain field (`item_id_list`) on the voucher itself rather
 * than a separate item-management endpoint family, so no sub-accessor is
 * needed here.
 *
 * @see .shopee-docs/API Reference/Voucher
 * @see .docs/api/Voucher
 */
final class Voucher
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @see .shopee-docs/API Reference/Voucher/add_voucher.md
     * @param array<string, mixed> $params voucher_name, voucher_code, start_time, end_time, voucher_type (1=shop, 2=product), reward_type (1=fix_amount, 2=discount_percentage, 3=coin_cashback), usage_quantity, min_basket_price (required); discount_amount, percentage, max_price, display_channel_list, item_id_list, display_start_time (optional, depending on voucher/reward type).
     * @return array<string, mixed>
     */
    public function addVoucher(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/voucher/add_voucher', $params);
    }

    /**
     * Once ongoing, only a limited subset of fields can be changed
     * (voucher_name, usage_quantity, end_time, display_channel_list, item_id_list).
     *
     * @see .shopee-docs/API Reference/Voucher/update_voucher.md
     * @param array<string, mixed> $params voucher_id (required); all other addVoucher() fields optional.
     * @return array<string, mixed>
     */
    public function updateVoucher(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/voucher/update_voucher', $params);
    }

    /**
     * Only an upcoming voucher can be deleted.
     *
     * @see .shopee-docs/API Reference/Voucher/delete_voucher.md
     * @param array<string, mixed> $params voucher_id (required).
     * @return array<string, mixed>
     */
    public function deleteVoucher(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/voucher/delete_voucher', $params);
    }

    /**
     * Only an ongoing voucher can be ended.
     *
     * @see .shopee-docs/API Reference/Voucher/end_voucher.md
     * @param array<string, mixed> $params voucher_id (required).
     * @return array<string, mixed>
     */
    public function endVoucher(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/voucher/end_voucher', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Voucher/get_voucher.md
     * @param array<string, mixed> $params voucher_id (required).
     * @return array<string, mixed>
     */
    public function getVoucher(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/voucher/get_voucher', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Voucher/get_voucher_list.md
     * @param array<string, mixed> $params status (required: upcoming/ongoing/expired/all); page_no, page_size (optional).
     * @return array<string, mixed>
     */
    public function getVoucherList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/voucher/get_voucher_list', $params);
    }
}
