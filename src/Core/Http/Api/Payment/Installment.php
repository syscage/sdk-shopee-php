<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Payment;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Buy-now-pay-later installment configuration, at the shop and item level.
 *
 * Accessed via `$shopee->payment()->installment()`.
 *
 * @see .shopee-docs/API Reference/Payment (get_item_installment_status, set_item_installment_status, get_shop_installment_status, set_shop_installment_status)
 * @see .docs/api/Payment/Installment.md
 */
final class Installment
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * TH/TW shops only.
     *
     * @see .shopee-docs/API Reference/Payment/get_item_installment_status.md
     * @param array<string, mixed> $params item_id_list (required, max 100).
     * @return array<string, mixed>
     */
    public function getItemInstallmentStatus(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/payment/get_item_installment_status', $params);
    }

    /**
     * TH/TW shops only.
     *
     * @see .shopee-docs/API Reference/Payment/set_item_installment_status.md
     * @param array<string, mixed> $params item_id_list, tenure_list (required, max 100); participate_plan_ahora (optional, AR only).
     * @return array<string, mixed>
     */
    public function setItemInstallmentStatus(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/payment/set_item_installment_status', $params);
    }

    /**
     * @see .shopee-docs/API Reference/Payment/get_shop_installment_status.md
     * @return array<string, mixed>
     */
    public function getShopInstallmentStatus(): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_shop_installment_status');
    }

    /**
     * @see .shopee-docs/API Reference/Payment/set_shop_installment_status.md
     * @param array<string, mixed> $params installment_status (required: 0 or 1).
     * @return array<string, mixed>
     */
    public function setShopInstallmentStatus(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/payment/set_shop_installment_status', $params);
    }
}
