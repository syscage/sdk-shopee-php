<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Order;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Buyer/local-seller invoices and FBS (Fulfilled By Shopee) tax documents.
 *
 * Accessed via `$shopee->order()->invoice()`.
 *
 * Unlike every other domain method in this SDK, {@see uploadInvoiceDoc()} and
 * {@see downloadInvoiceDoc()} do not take/return a plain params array — file
 * upload/download fundamentally isn't a JSON request/response, so forcing it
 * into that shape would hurt usability more than the inconsistency costs
 * (see `Http\Client::shopUpload()` / `shopDownload()`).
 *
 * @see .shopee-docs/API Reference/Order
 * @see .docs/api/Order/Invoice.md
 */
final class Invoice
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * VN/TH/PH local sellers only.
     *
     * @see .shopee-docs/API Reference/Order/get_buyer_invoice_info.md
     * @param array<string, mixed> $params queries (required): [{order_sn}].
     * @return array<string, mixed>
     */
    public function getBuyerInvoiceInfo(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/order/get_buyer_invoice_info', $params);
    }

    /**
     * PH/BR local sellers only.
     *
     * @see .shopee-docs/API Reference/Order/get_pending_buyer_invoice_order_list.md
     * @param array<string, mixed> $params page_size (required); cursor (optional).
     * @return array<string, mixed>
     */
    public function getPendingBuyerInvoiceOrderList(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/order/get_pending_buyer_invoice_order_list', $params);
    }

    /**
     * PH/BR local sellers only. File size limit 1MB.
     *
     * @see .shopee-docs/API Reference/Order/upload_invoice_doc.md
     * @param int $fileType 1=pdf, 2=jpeg, 3=png, 4=xml.
     * @return array<string, mixed>
     */
    public function uploadInvoiceDoc(string $orderSn, int $fileType, string $filePath): array
    {
        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException(sprintf('Unable to read file for upload: %s', $filePath));
        }

        return $this->client->shopUpload(
            '/api/v2/order/upload_invoice_doc',
            ['order_sn' => $orderSn, 'file_type' => $fileType],
            'file',
            fopen($filePath, 'rb'),
            basename($filePath),
        );
    }

    /**
     * PH/BR local sellers only.
     *
     * @return string Raw invoice file bytes. Shopee does not report the file's
     *                 content type/extension in the response — track it yourself
     *                 from what was originally uploaded via {@see uploadInvoiceDoc()}.
     *
     * @see .shopee-docs/API Reference/Order/download_invoice_doc.md
     */
    public function downloadInvoiceDoc(string $orderSn): string
    {
        return $this->client->shopDownload('/api/v2/order/download_invoice_doc', ['order_sn' => $orderSn]);
    }

    /**
     * Step 1 of 3: request an async batch of FBS tax documents.
     * Poll {@see getFbsInvoicesResult()}, then {@see downloadFbsInvoices()}.
     *
     * @see .shopee-docs/API Reference/Order/generate_fbs_invoices.md
     * @param array<string, mixed> $params batch_download (optional): {start, end, document_type, file_type, document_status}.
     * @return array<string, mixed>
     */
    public function generateFbsInvoices(array $params = []): array
    {
        return $this->client->shop('POST', '/api/v2/order/generate_fbs_invoices', $params);
    }

    /**
     * Step 2 of 3: poll until status is "READY".
     *
     * @see .shopee-docs/API Reference/Order/get_fbs_invoices_result.md
     * @param array<string, mixed> $params request_id_list (required): {request_id: int64[]}.
     * @return array<string, mixed>
     */
    public function getFbsInvoicesResult(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/order/get_fbs_invoices_result', $params);
    }

    /**
     * Step 3 of 3. Despite the name, the response is JSON containing a
     * `file_link` — fetch the actual file bytes from that URL yourself.
     *
     * @see .shopee-docs/API Reference/Order/download_fbs_invoices.md
     * @param array<string, mixed> $params request_id_list (optional): {request_id: int64[]}.
     * @return array<string, mixed>
     */
    public function downloadFbsInvoices(array $params = []): array
    {
        return $this->client->shop('POST', '/api/v2/order/download_fbs_invoices', $params);
    }
}
