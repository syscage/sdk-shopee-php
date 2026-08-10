<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api\Logistics;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Logistics\ShippingDocument;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ShippingDocumentTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'createShippingDocument' => ['createShippingDocument', 'POST', '/api/v2/logistics/create_shipping_document', ['order_list' => []]],
            'createShippingDocumentJob' => ['createShippingDocumentJob', 'POST', '/api/v2/logistics/create_shipping_document_job', ['shipping_document_type' => 'x']],
            'downloadShippingDocument' => ['downloadShippingDocument', 'POST', '/api/v2/logistics/download_shipping_document', ['order_list' => []]],
            'downloadToLabel' => ['downloadToLabel', 'POST', '/api/v2/logistics/download_to_label', ['sorting_group' => 1]],
            'getShippingDocumentDataInfo' => ['getShippingDocumentDataInfo', 'POST', '/api/v2/logistics/get_shipping_document_data_info', ['order_sn' => '1']],
            'getShippingDocumentJobStatus' => ['getShippingDocumentJobStatus', 'POST', '/api/v2/logistics/get_shipping_document_job_status', ['job_id' => '1']],
            'getShippingDocumentParameter' => ['getShippingDocumentParameter', 'POST', '/api/v2/logistics/get_shipping_document_parameter', ['order_list' => []]],
            'getShippingDocumentResult' => ['getShippingDocumentResult', 'POST', '/api/v2/logistics/get_shipping_document_result', ['order_list' => []]],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new ShippingDocument($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testDownloadShippingDocumentJobSendsJobIdAndReturnsRawBytes(): void
    {
        $client = $this->makeClient([
            new Response(200, [], '%PDF-1.4 raw label bytes'),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        $bytes = (new ShippingDocument($client))->downloadShippingDocumentJob('job-123');

        $this->assertSame('%PDF-1.4 raw label bytes', $bytes);
        $this->assertSame('POST', $this->lastRequest()->getMethod());
        $this->assertSame('/api/v2/logistics/download_shipping_document_job', $this->lastRequest()->getUri()->getPath());
        $this->assertSame(['job_id' => 'job-123'], $this->lastRequestBodyAsArray());
    }
}
