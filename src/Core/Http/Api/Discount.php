<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\Discount\Sip;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Discount API domain — shop-level flash-sale-style discount promotions.
 *
 * Item-level management (`addDiscountItem()`, `updateDiscountItem()`,
 * `deleteDiscountItem()`) stays on this class rather than a sub-accessor,
 * matching how Product keeps model management alongside item management —
 * an item_list is an intrinsic part of a discount, not a distinct concept.
 *
 * SIP (cross-border) overseas discount rates are a genuinely distinct,
 * region-keyed concept and live behind {@see sip()}.
 *
 * @see .shopee-docs/API Reference/Discount
 * @see .docs/api/Discount
 */
final class Discount
{
    private ?Sip $sip = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function sip(): Sip
    {
        return $this->sip ??= new Sip($this->client);
    }

    // ------------------------------------------------------------------
    // Discount
    // ------------------------------------------------------------------

    /**
     * Start time must be at least 1 hour in the future; the promotion
     * period must be under 180 days.
     *
     * @see .shopee-docs/API Reference/Discount/add_discount.md
     * @param array<string, mixed> $params discount_name, start_time, end_time (required).
     * @return array<string, mixed>
     */
    public function addDiscount(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/discount/add_discount', $params);
    }

    /**
     * The start time can only be moved later, and only while the promotion
     * hasn't started; the end time can only be moved earlier.
     *
     * @see .shopee-docs/API Reference/Discount/update_discount.md
     * @param array<string, mixed> $params discount_id (required); discount_name, start_time, end_time (optional).
     * @return array<string, mixed>
     */
    public function updateDiscount(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/discount/update_discount', $params);
    }

    /**
     * An ongoing discount cannot be deleted — {@see endDiscount()} it first.
     *
     * @see .shopee-docs/API Reference/Discount/delete_discount.md
     * @param array<string, mixed> $params discount_id (required).
     * @return array<string, mixed>
     */
    public function deleteDiscount(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/discount/delete_discount', $params);
    }

    /**
     * Only an ongoing discount can be ended, and only after 1 hour past its start time.
     *
     * @see .shopee-docs/API Reference/Discount/end_discount.md
     * @param array<string, mixed> $params discount_id (required).
     * @return array<string, mixed>
     */
    public function endDiscount(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/discount/end_discount', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Discount/get_discount.md
     * @param array<string, mixed> $params discount_id, page_no, page_size (required).
     * @return array<string, mixed>
     */
    public function getDiscount(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/discount/get_discount', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Discount/get_discount_list.md
     * @param array<string, mixed> $params discount_status, page_no, page_size (required: discount_status one of upcoming/ongoing/expired/all); update_time_from, update_time_to (optional, max 30-day window).
     * @return array<string, mixed>
     */
    public function getDiscountList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/discount/get_discount_list', $params);
    }

    // ------------------------------------------------------------------
    // Discount Items
    // ------------------------------------------------------------------

    /**
     * Up to 50 items per call.
     *
     * @see .shopee-docs/API Reference/Discount/add_discount_item.md
     * @param array<string, mixed> $params discount_id, item_list (required): [{item_id, purchase_limit, item_promotion_price, model_list}].
     * @return array<string, mixed>
     */
    public function addDiscountItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/discount/add_discount_item', $params);
    }

    /**
     * Up to 50 items per call. Reserved promotion stock cannot be changed —
     * delete and re-add the item to change it.
     *
     * @see .shopee-docs/API Reference/Discount/update_discount_item.md
     * @param array<string, mixed> $params discount_id, item_list (required): [{item_id, purchase_limit, item_promotion_price, model_list}].
     * @return array<string, mixed>
     */
    public function updateDiscountItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/discount/update_discount_item', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Discount/delete_discount_item.md
     * @param array<string, mixed> $params discount_id, item_id (required); model_id (optional).
     * @return array<string, mixed>
     */
    public function deleteDiscountItem(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/discount/delete_discount_item', $params);
    }
}
