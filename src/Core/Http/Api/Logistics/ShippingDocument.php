<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Logistics;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Shipping document (AWB/waybill/label) generation and download.
 *
 * Accessed via `$shopee->logistics()->shippingDocument()`.
 *
 * {@see downloadShippingDocument()}, {@see downloadShippingDocumentJob()},
 * and {@see downloadToLabel()} return raw file bytes on success but Shopee
 * still responds with a JSON error body on failure — unlike Order's
 * download endpoints these are POST requests with a JSON body, so they go
 * through `Http\Client::shopDownloadPost()` rather than `shopDownload()`.
 *
 * @see .shopee-docs/API Reference/Logistics (create_shipping_document, download_shipping_document, ...)
 * @see .docs/api/Logistics/ShippingDocument.md
 */
final class ShippingDocument
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * Create an async AWB generation task for orders/packages.
     * Poll {@see getShippingDocumentResult()}.
     *
     * @see .shopee-docs/API Reference/Logistics/create_shipping_document.md
     * @param array<string, mixed> $params order_list (required): [{order_sn, package_number, tracking_number, shipping_document_type}].
     * @return array<string, mixed>
     */
    public function createShippingDocument(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/create_shipping_document', $params);
    }

    /**
     * Create an async job for bulk/unpackaged-SKU labels.
     * Poll {@see getShippingDocumentJobStatus()}, then {@see downloadShippingDocumentJob()}.
     *
     * @see .shopee-docs/API Reference/Logistics/create_shipping_document_job.md
     * @param array<string, mixed> $params shipping_document_type (required); unpackaged_sku_requests, package_list (optional).
     * @return array<string, mixed>
     */
    public function createShippingDocumentJob(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/create_shipping_document_job', $params);
    }

    /**
     * @return string Raw waybill/AWB file bytes.
     *
     * @see .shopee-docs/API Reference/Logistics/download_shipping_document.md
     * @param array<string, mixed> $params order_list (required); shipping_document_type (optional).
     */
    public function downloadShippingDocument(array $params): string
    {
        return $this->client->shopDownloadPost('/api/v2/logistics/download_shipping_document', $params);
    }

    /**
     * @return string Raw file bytes for a job created via {@see createShippingDocumentJob()}.
     *
     * @see .shopee-docs/API Reference/Logistics/download_shipping_document_job.md
     */
    public function downloadShippingDocumentJob(string $jobId): string
    {
        return $this->client->shopDownloadPost('/api/v2/logistics/download_shipping_document_job', ['job_id' => $jobId]);
    }

    /**
     * Taiwan channel 30029 only.
     *
     * @return string Raw PDF waybill bytes.
     *
     * @see .shopee-docs/API Reference/Logistics/download_to_label.md
     * @param array<string, mixed> $params sorting_group (required); quantity (optional).
     */
    public function downloadToLabel(array $params): string
    {
        return $this->client->shopDownloadPost('/api/v2/logistics/download_to_label', $params);
    }

    /**
     * Raw logistics fields for a self-designed AWB. Optional rendered
     * address images are returned as base64 PNG strings within the JSON —
     * not a file download.
     *
     * @see .shopee-docs/API Reference/Logistics/get_shipping_document_data_info.md
     * @param array<string, mixed> $params order_sn (required); package_number, recipient_address_info (optional).
     * @return array<string, mixed>
     */
    public function getShippingDocumentDataInfo(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/get_shipping_document_data_info', $params);
    }

    /**
     * Poll status of a job from {@see createShippingDocumentJob()}. Statuses: PROCESSING/READY/EXPIRED/FAILED.
     *
     * @see .shopee-docs/API Reference/Logistics/get_shipping_document_job_status.md
     * @param array<string, mixed> $params job_id (required).
     * @return array<string, mixed>
     */
    public function getShippingDocumentJobStatus(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/get_shipping_document_job_status', $params);
    }

    /**
     * Get the selectable/suggested `shipping_document_type` per order.
     *
     * @see .shopee-docs/API Reference/Logistics/get_shipping_document_parameter.md
     * @param array<string, mixed> $params order_list (required).
     * @return array<string, mixed>
     */
    public function getShippingDocumentParameter(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/get_shipping_document_parameter', $params);
    }

    /**
     * Poll status of a task from {@see createShippingDocument()}. Statuses: READY/FAILED/PROCESSING.
     *
     * @see .shopee-docs/API Reference/Logistics/get_shipping_document_result.md
     * @param array<string, mixed> $params order_list (required).
     * @return array<string, mixed>
     */
    public function getShippingDocumentResult(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/logistics/get_shipping_document_result', $params);
    }
}
