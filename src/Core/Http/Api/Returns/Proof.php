<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Api\Returns;

use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Evidence/proof images attached to a return case. `convertImage()` is the
 * domain's only true file-upload endpoint (multipart, sanctioned exception
 * to the uniform `array $params` convention — same rationale as
 * `Order\Invoice::uploadInvoiceDoc()`); its `url`/`thumbnail` response is
 * meant to feed `uploadProof()`'s `photo` array (and `Returns::dispute()`'s
 * `image_list`).
 *
 * Not to be confused with {@see Shipping::uploadShippingProof()}, which
 * uses images obtained separately from `v2.media.upload_image`
 * (`Http\Api\Media::uploadImage()`), not from `convertImage()`.
 *
 * @see .shopee-docs/API Reference/Returns
 * @see .docs/api/Returns/Proof
 */
final class Proof
{
    public function __construct(private readonly Client $client)
    {
    }

    /**
     * @param array<string, mixed> $params return_sn (required).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/query_proof.md
     */
    public function queryProof(array $params): array
    {
        return $this->client->shop('GET', '/api/v2/returns/query_proof', $params);
    }

    /**
     * @param array<string, mixed> $params return_sn (required); photo
     *     (optional array of {url, thumbnail}, `url` should come from
     *     {@see convertImage()}), description (optional).
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/upload_proof.md
     */
    public function uploadProof(array $params): array
    {
        return $this->client->shop('POST', '/api/v2/returns/upload_proof', $params);
    }

    /**
     * Upload a raw proof image (max 10MB, JPG/JPEG/PNG) and get back a
     * hosted `url`/`thumbnail` to pass into {@see uploadProof()} or
     * `Returns::dispute()`.
     *
     * @return array<string, mixed>
     *
     * @see .shopee-docs/API Reference/Returns/convert_image.md
     */
    public function convertImage(string $returnSn, string $filePath): array
    {
        if (!is_readable($filePath)) {
            throw new \InvalidArgumentException(sprintf('Unable to read file for upload: %s', $filePath));
        }

        return $this->client->shopUpload(
            '/api/v2/returns/convert_image',
            ['return_sn' => $returnSn],
            'upload_image',
            fopen($filePath, 'rb'),
            basename($filePath),
        );
    }
}
