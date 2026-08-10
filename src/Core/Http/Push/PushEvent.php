<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Push;

use Syscage\Sdk\Shopee\Core\Exceptions\PushPayloadException;

/**
 * A parsed Shopee push notification.
 *
 * Shopee's push envelope is not consistent across push types (confirmed by
 * reading every doc under `.shopee-docs/Push Mechanism/`): `shop_id`,
 * `merchant_id`, and `partner_id` each appear at the top level for some
 * push types and only inside `data` for others; the Consignment Service
 * Push category uses `supplier_id` instead of `shop_id` entirely; and a
 * few docs disagree with their own examples on field names (e.g.
 * `shipping_document_status_push` documents `order_sn` but its own example
 * uses `ordersn`). Modeling one class per push type against documentation
 * that's this inconsistent would mean chasing broken examples into
 * fragile, over-specific types. Instead this is a single generic envelope
 * — `code`/`timestamp` are always present and typed; `data` and `raw` are
 * exposed as plain arrays for the consumer to read defensively, matching
 * this SDK's "array in, array out" convention for API responses.
 *
 * @see .shopee-docs/Push Mechanism/
 * @see .docs/push/README.md
 */
final class PushEvent
{
    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $raw
     */
    private function __construct(
        public readonly int $code,
        public readonly int $timestamp,
        public readonly array $data,
        public readonly array $raw,
    ) {
    }

    /**
     * @throws PushPayloadException if the body isn't valid JSON, isn't a
     *     JSON object, or is missing an integer `code`/`timestamp`.
     */
    public static function fromJson(string $rawBody): self
    {
        $decoded = json_decode($rawBody, true);

        if (!is_array($decoded)) {
            throw new PushPayloadException('Push payload is not a valid JSON object.');
        }

        if (!isset($decoded['code']) || !is_int($decoded['code'])) {
            throw new PushPayloadException('Push payload is missing an integer "code" field.');
        }

        if (!isset($decoded['timestamp']) || !is_int($decoded['timestamp'])) {
            throw new PushPayloadException('Push payload is missing an integer "timestamp" field.');
        }

        $data = $decoded['data'] ?? [];

        return new self(
            code: $decoded['code'],
            timestamp: $decoded['timestamp'],
            data: is_array($data) ? $data : [],
            raw: $decoded,
        );
    }

    /**
     * The typed event code, or `null` if this is a code Shopee has
     * introduced since this SDK was last updated — {@see $code} still
     * holds the raw integer either way.
     */
    public function eventCode(): ?PushEventCode
    {
        return PushEventCode::tryFrom($this->code);
    }

    /**
     * Checks the top level first, then `data` — Shopee places `shop_id` in
     * different places depending on push type. Returns `null` for push
     * types that carry no single shop scope at all (shop (de)authorization,
     * open API authorization expiry, video upload result, and every
     * Consignment Service Push — see {@see supplierId()} for that last one).
     */
    public function shopId(): ?int
    {
        return $this->intField('shop_id');
    }

    public function merchantId(): ?int
    {
        return $this->intField('merchant_id');
    }

    public function partnerId(): ?int
    {
        return $this->intField('partner_id');
    }

    /**
     * Only Consignment Service Push events (`supplier_create_product`,
     * `supplier_product_review_result`, `purchase_order`,
     * `inbound_status`) carry this — that category has no `shop_id` at all.
     */
    public function supplierId(): ?int
    {
        return $this->intField('supplier_id');
    }

    private function intField(string $key): ?int
    {
        $value = $this->raw[$key] ?? $this->data[$key] ?? null;

        return is_int($value) ? $value : null;
    }
}
