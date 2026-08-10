<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Payment;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class PaymentTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function shopLevelEndpoints(): array
    {
        return [
            'getEscrowDetail' => ['getEscrowDetail', 'GET', '/api/v2/payment/get_escrow_detail', ['order_sn' => '1']],
            'getEscrowDetailBatch' => ['getEscrowDetailBatch', 'GET', '/api/v2/payment/get_escrow_detail_batch', ['order_sn_list' => ['1']]],
            'getEscrowList' => ['getEscrowList', 'GET', '/api/v2/payment/get_escrow_list', ['release_time_from' => 1, 'release_time_to' => 2]],

            'getIncomeOverview' => ['getIncomeOverview', 'GET', '/api/v2/payment/get_income_overview', []],
            'getIncomeDetail' => ['getIncomeDetail', 'GET', '/api/v2/payment/get_income_detail', ['date_from' => '2024-01-01', 'date_to' => '2024-01-02', 'income_status' => 1, 'page_size' => 10]],
            'generateIncomeReport' => ['generateIncomeReport', 'GET', '/api/v2/payment/generate_income_report', ['release_time_from' => 1, 'release_time_to' => 2]],
            'getIncomeReport' => ['getIncomeReport', 'GET', '/api/v2/payment/get_income_report', ['income_report_id' => 1]],
            'generateIncomeStatement' => ['generateIncomeStatement', 'GET', '/api/v2/payment/generate_income_statement', ['release_time_from' => 1, 'release_time_to' => 2, 'statement_type' => 1]],
            'getIncomeStatement' => ['getIncomeStatement', 'GET', '/api/v2/payment/get_income_statement', ['income_statement_id' => 1]],

            'getPayoutDetail' => ['getPayoutDetail', 'GET', '/api/v2/payment/get_payout_detail', ['page_size' => 10, 'page_no' => 1, 'payout_time_from' => 1, 'payout_time_to' => 2]],
            'getPayoutInfo' => ['getPayoutInfo', 'GET', '/api/v2/payment/get_payout_info', ['payout_time_from' => 1, 'payout_time_to' => 2, 'page_size' => 10, 'cursor' => '']],
            'getBillingTransactionInfo' => ['getBillingTransactionInfo', 'POST', '/api/v2/payment/get_billing_transaction_info', ['billing_transaction_info_type' => 1, 'cursor' => '', 'page_size' => 10]],

            'getWalletTransactionList' => ['getWalletTransactionList', 'GET', '/api/v2/payment/get_wallet_transaction_list', ['page_no' => 1, 'page_size' => 10]],
        ];
    }

    #[DataProvider('shopLevelEndpoints')]
    public function testShopLevelEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Payment($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
        $this->assertSame('14701711', $this->lastRequestQuery()['shop_id']);
    }

    public function testGetPaymentMethodListSignsAtPublicLevelWithoutAccessToken(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ]);

        (new Payment($client))->getPaymentMethodList();

        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/payment/get_payment_method_list', $this->lastRequest()->getUri()->getPath());

        $query = $this->lastRequestQuery();
        $this->assertArrayHasKey('partner_id', $query);
        $this->assertArrayNotHasKey('access_token', $query);
        $this->assertArrayNotHasKey('shop_id', $query);
    }

    public function testInstallmentIsLazilyCachedPerInstance(): void
    {
        $payment = new Payment($this->makeClient([]));

        $this->assertSame($payment->installment(), $payment->installment());
    }
}
