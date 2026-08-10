<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api;

use Syscage\Sdk\Shopee\Core\Http\Api\Payment\Installment;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Payment API domain — order-level accounting (escrow), income
 * reports/statements, payouts, and wallet transactions.
 *
 * Every method here is Shop-level, except {@see getPaymentMethodList()},
 * which Shopee's own documentation states requires no authentication at
 * all — it signs at the Public level via {@see Client::public()}.
 *
 * Buy-now-pay-later installment configuration is a distinct enough concern
 * to live behind its own accessor — {@see installment()}.
 *
 * @see .shopee-docs/API Reference/Payment
 * @see .docs/api/Payment
 */
final class Payment
{
    private ?Installment $installment = null;

    public function __construct(private readonly Client $client)
    {
    }

    public function installment(): Installment
    {
        return $this->installment ??= new Installment($this->client);
    }

    // ------------------------------------------------------------------
    // Escrow
    // ------------------------------------------------------------------

    /**
     * Accounting detail (fees, discounts, taxes, escrow amount) for one order.
     *
     * @see .shopee-docs/API Reference/Payment/get_escrow_detail.md
     * @param array<string, mixed> $params order_sn (required).
     * @return array<string, mixed>
     */
    public function getEscrowDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_escrow_detail', $params);
    }

    /**
     * Batch variant of {@see getEscrowDetail()}.
     *
     * @see .shopee-docs/API Reference/Payment/get_escrow_detail_batch.md
     * @param array<string, mixed> $params order_sn_list (required, max 50).
     * @return array<string, mixed>
     */
    public function getEscrowDetailBatch(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_escrow_detail_batch', $params);
    }

    /**
     * List of orders with their payout amount and escrow release time.
     *
     * @see .shopee-docs/API Reference/Payment/get_escrow_list.md
     * @param array<string, mixed> $params release_time_from, release_time_to (required); page_size, page_no (optional).
     * @return array<string, mixed>
     */
    public function getEscrowList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_escrow_list', $params);
    }

    // ------------------------------------------------------------------
    // Income
    // ------------------------------------------------------------------

    /**
     * Snapshot of income by status (pending/to-release/released) — mirrors
     * Seller Center's "Income Overview". Historical results aren't retrievable.
     *
     * @see .shopee-docs/API Reference/Payment/get_income_overview.md
     * @param array<string, mixed> $params income_status (optional).
     * @return array<string, mixed>
     */
    public function getIncomeOverview(array $params = []): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_income_overview', $params);
    }

    /**
     * Order-level income detail by status/date range — mirrors Seller
     * Center's "Income Details".
     *
     * @see .shopee-docs/API Reference/Payment/get_income_detail.md
     * @param array<string, mixed> $params date_from, date_to, income_status, page_size (required); cursor (optional).
     * @return array<string, mixed>
     */
    public function getIncomeDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_income_detail', $params);
    }

    /**
     * Trigger generation of a downloadable income report. Poll
     * {@see getIncomeReport()} with the returned `id` as `income_report_id`.
     *
     * @see .shopee-docs/API Reference/Payment/generate_income_report.md
     * @param array<string, mixed> $params release_time_from, release_time_to (required).
     * @return array<string, mixed>
     */
    public function generateIncomeReport(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/generate_income_report', $params);
    }

    /**
     * Poll status of a report from {@see generateIncomeReport()}. status: 0
     * invalid, 1 processing, 2 downloadable, 3 downloaded, 4 failed —
     * `file_link` is present once downloadable.
     *
     * @see .shopee-docs/API Reference/Payment/get_income_report.md
     * @param array<string, mixed> $params income_report_id (required).
     * @return array<string, mixed>
     */
    public function getIncomeReport(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_income_report', $params);
    }

    /**
     * Trigger generation of a downloadable income statement. Poll
     * {@see getIncomeStatement()} with the returned `id` as `income_statement_id`.
     *
     * @see .shopee-docs/API Reference/Payment/generate_income_statement.md
     * @param array<string, mixed> $params release_time_from, release_time_to, statement_type (required).
     * @return array<string, mixed>
     */
    public function generateIncomeStatement(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/generate_income_statement', $params);
    }

    /**
     * Poll status of a statement from {@see generateIncomeStatement()} — same
     * status enum/`file_link` shape as {@see getIncomeReport()}.
     *
     * @see .shopee-docs/API Reference/Payment/get_income_statement.md
     * @param array<string, mixed> $params income_statement_id (required).
     * @return array<string, mixed>
     */
    public function getIncomeStatement(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_income_statement', $params);
    }

    // ------------------------------------------------------------------
    // Payout & Billing
    // ------------------------------------------------------------------

    /**
     * @deprecated Shopee documents this as replaced by {@see getPayoutInfo()}.
     *
     * @see .shopee-docs/API Reference/Payment/get_payout_detail.md
     * @param array<string, mixed> $params page_size, page_no, payout_time_from, payout_time_to (required, max 15-day window).
     * @return array<string, mixed>
     */
    public function getPayoutDetail(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_payout_detail', $params);
    }

    /**
     * Cross-border seller payout data. Each entry's `encrypted_payout_id`
     * feeds {@see getBillingTransactionInfo()}'s `encrypted_payout_ids`.
     *
     * @see .shopee-docs/API Reference/Payment/get_payout_info.md
     * @param array<string, mixed> $params payout_time_from, payout_time_to (required, max 15-day window), page_size, cursor (required).
     * @return array<string, mixed>
     */
    public function getPayoutInfo(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_payout_info', $params);
    }

    /**
     * Billing transaction detail (to-release/released) for cross-border
     * payouts, keyed by the `encrypted_payout_id`(s) from {@see getPayoutInfo()}.
     *
     * @see .shopee-docs/API Reference/Payment/get_billing_transaction_info.md
     * @param array<string, mixed> $params billing_transaction_info_type, cursor, page_size (required); encrypted_payout_ids (optional, max 100).
     * @return array<string, mixed>
     */
    public function getBillingTransactionInfo(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/payment/get_billing_transaction_info', $params);
    }

    // ------------------------------------------------------------------
    // Wallet
    // ------------------------------------------------------------------

    /**
     * Local shops only.
     *
     * @see .shopee-docs/API Reference/Payment/get_wallet_transaction_list.md
     * @param array<string, mixed> $params page_no, page_size (required); create_time_from, create_time_to (optional, max 15-day window), wallet_type, transaction_type, money_flow, transaction_tab_type (optional).
     * @return array<string, mixed>
     */
    public function getWalletTransactionList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/payment/get_wallet_transaction_list', $params);
    }

    // ------------------------------------------------------------------
    // Payment Method
    // ------------------------------------------------------------------

    /**
     * Available payment methods by region. Shopee's own documentation
     * states this endpoint requires no authentication — it signs at the
     * Public level (no `access_token`/`shop_id`), unlike every other method
     * on this class.
     *
     * @see .shopee-docs/API Reference/Payment/get_payment_method_list.md
     * @return array<string, mixed>
     */
    public function getPaymentMethodList(): array
    {
        return $this->client->public('GET', '/api/v2/payment/get_payment_method_list');
    }
}
