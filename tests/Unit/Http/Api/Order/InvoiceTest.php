<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Order;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Order\Invoice;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class InvoiceTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function jsonEndpoints(): array
    {
        return [
            'getBuyerInvoiceInfo' => ['getBuyerInvoiceInfo', 'POST', '/api/v2/order/get_buyer_invoice_info', ['queries' => [['order_sn' => '1']]]],
            'getPendingBuyerInvoiceOrderList' => ['getPendingBuyerInvoiceOrderList', 'GET', '/api/v2/order/get_pending_buyer_invoice_order_list', ['page_size' => 10]],
            'generateFbsInvoices' => ['generateFbsInvoices', 'POST', '/api/v2/order/generate_fbs_invoices', []],
            'getFbsInvoicesResult' => ['getFbsInvoicesResult', 'POST', '/api/v2/order/get_fbs_invoices_result', ['request_id_list' => ['request_id' => [1]]]],
            'downloadFbsInvoices' => ['downloadFbsInvoices', 'POST', '/api/v2/order/download_fbs_invoices', []],
        ];
    }

    #[DataProvider('jsonEndpoints')]
    public function testJsonEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Invoice($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testUploadInvoiceDocSendsMultipartRequest(): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        $tmpFile = tempnam(sys_get_temp_dir(), 'shopee-sdk-test-');
        file_put_contents($tmpFile, 'fake pdf bytes');

        try {
            (new Invoice($client))->uploadInvoiceDoc('ORDER123', 1, $tmpFile);

            $request = $this->lastRequest();
            $this->assertSame('POST', $request->getMethod());
            $this->assertSame('/api/v2/order/upload_invoice_doc', $request->getUri()->getPath());
            $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));

            $body = (string) $request->getBody();
            $this->assertStringContainsString('name="order_sn"', $body);
            $this->assertStringContainsString('ORDER123', $body);
            $this->assertStringContainsString('name="file_type"', $body);
            $this->assertStringContainsString('name="file"; filename="' . basename($tmpFile) . '"', $body);
            $this->assertStringContainsString('fake pdf bytes', $body);
        } finally {
            unlink($tmpFile);
        }
    }

    public function testUploadInvoiceDocThrowsOnUnreadableFile(): void
    {
        $client = $this->makeClient([])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 1));

        $this->expectException(\InvalidArgumentException::class);

        (new Invoice($client))->uploadInvoiceDoc('ORDER123', 1, '/nonexistent/path/does-not-exist.pdf');
    }

    public function testDownloadInvoiceDocReturnsRawBytes(): void
    {
        $client = $this->makeClient([
            new Response(200, ['Content-Type' => 'application/pdf'], 'raw invoice bytes'),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        $bytes = (new Invoice($client))->downloadInvoiceDoc('ORDER123');

        $this->assertSame('raw invoice bytes', $bytes);
        $this->assertSame('GET', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/order/download_invoice_doc', $this->lastRequest()->getUri()->getPath());
        $this->assertSame('ORDER123', $this->lastRequestQuery()['order_sn']);
    }
}
