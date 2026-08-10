<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\MultipartStream;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Syscage\Sdk\Shopee\Core\Exceptions\ApiException;
use Syscage\Sdk\Shopee\Core\Exceptions\TransportException;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;
use Syscage\Sdk\Shopee\Core\Http\Auth\Signer;

/**
 * Signed HTTP transport for the Shopee Open Platform.
 *
 * Every API domain class is built on top of this client rather than talking
 * HTTP directly, so request signing and error handling live in exactly one place.
 */
final class Client
{
    private readonly ClientInterface $httpClient;
    private readonly RequestFactoryInterface $requestFactory;
    private readonly StreamFactoryInterface $streamFactory;
    private readonly Signer $signer;

    public function __construct(
        private readonly Credentials $credentials,
        private ?AccessToken $accessToken = null,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null,
        ?Signer $signer = null,
    ) {
        $factory = new HttpFactory();

        $this->httpClient = $httpClient ?? new GuzzleClient();
        $this->requestFactory = $requestFactory ?? $factory;
        $this->streamFactory = $streamFactory ?? $factory;
        $this->signer = $signer ?? new Signer();
    }

    public function withAccessToken(AccessToken $accessToken): self
    {
        $clone = clone $this;
        $clone->accessToken = $accessToken;

        return $clone;
    }

    public function getAccessToken(): ?AccessToken
    {
        return $this->accessToken;
    }

    /**
     * Call a Public API endpoint (no access token required).
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function public(string $method, string $path, array $params = []): array
    {
        $timestamp = time();
        $baseString = $this->signer->publicBaseString($this->credentials->partnerId, $path, $timestamp);

        $common = [
            'partner_id' => $this->credentials->partnerId,
            'timestamp' => $timestamp,
            'sign' => $this->signer->sign($this->credentials->partnerKey, $baseString),
        ];

        return $this->send($method, $path, $common, $params);
    }

    /**
     * Call a Shop API endpoint. Requires an access token, via {@see withAccessToken()}.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function shop(string $method, string $path, array $params = [], ?int $shopId = null): array
    {
        return $this->send($method, $path, $this->shopCommonParams($path, $shopId), $params);
    }

    /**
     * Call a Merchant API endpoint (cross-border sellers only). Requires an
     * access token, via {@see withAccessToken()}.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function merchant(string $method, string $path, array $params = [], ?int $merchantId = null): array
    {
        $accessToken = $this->requireAccessToken();
        $merchantId ??= $accessToken->merchantId;

        if ($merchantId === null) {
            throw new \InvalidArgumentException('A merchant_id is required to call a Merchant API and none was provided or found on the access token.');
        }

        $timestamp = time();
        $baseString = $this->signer->merchantBaseString(
            $this->credentials->partnerId,
            $path,
            $timestamp,
            $accessToken->accessToken,
            $merchantId,
        );

        $common = [
            'partner_id' => $this->credentials->partnerId,
            'timestamp' => $timestamp,
            'sign' => $this->signer->sign($this->credentials->partnerKey, $baseString),
            'access_token' => $accessToken->accessToken,
            'merchant_id' => $merchantId,
        ];

        return $this->send($method, $path, $common, $params);
    }

    /**
     * Call a Principal API endpoint (BrandPortal's brand-owner analytics,
     * spanning multiple shops/regions under one principal). Requires an
     * access token, via {@see withAccessToken()}.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function principal(string $method, string $path, array $params = [], ?int $principalId = null): array
    {
        $accessToken = $this->requireAccessToken();
        $principalId ??= $accessToken->principalId;

        if ($principalId === null) {
            throw new \InvalidArgumentException('A principal_id is required to call a Principal API and none was provided or found on the access token.');
        }

        $timestamp = time();
        $baseString = $this->signer->principalBaseString(
            $this->credentials->partnerId,
            $path,
            $timestamp,
            $accessToken->accessToken,
            $principalId,
        );

        $common = [
            'partner_id' => $this->credentials->partnerId,
            'timestamp' => $timestamp,
            'sign' => $this->signer->sign($this->credentials->partnerKey, $baseString),
            'access_token' => $accessToken->accessToken,
            'principal_id' => $principalId,
        ];

        return $this->send($method, $path, $common, $params);
    }

    /**
     * Call a User API endpoint (e.g. Video's content/analytics endpoints,
     * scoped to the authorizing user rather than a shop). Requires an
     * access token, via {@see withAccessToken()}.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function user(string $method, string $path, array $params = [], ?int $userId = null): array
    {
        return $this->send($method, $path, $this->userCommonParams($path, $userId), $params);
    }

    /**
     * Upload a file to a User API endpoint as `multipart/form-data` (e.g.
     * Livestream's cover image upload).
     *
     * @param array<string, scalar> $fields
     * @param resource|string|\Psr\Http\Message\StreamInterface $fileContents
     * @return array<string, mixed>
     */
    public function userUpload(
        string $path,
        array $fields,
        string $fileFieldName,
        mixed $fileContents,
        string $fileName,
        ?int $userId = null,
    ): array {
        $uri = $this->credentials->apiUrl . $path . '?' . http_build_query($this->userCommonParams($path, $userId));
        $request = $this->buildMultipartRequest($uri, $fields, [
            ['name' => $fileFieldName, 'contents' => $fileContents, 'filename' => $fileName],
        ]);

        return $this->decodeJson($this->sendRequest($request));
    }

    /**
     * Upload a file to a Shop API endpoint as `multipart/form-data`.
     *
     * The endpoint's non-file request parameters (e.g. `order_sn`) are sent
     * as additional multipart fields alongside the file, matching Shopee's
     * documented upload endpoints.
     *
     * @param array<string, scalar> $fields
     * @param resource|string|\Psr\Http\Message\StreamInterface $fileContents
     * @return array<string, mixed>
     */
    public function shopUpload(
        string $path,
        array $fields,
        string $fileFieldName,
        mixed $fileContents,
        string $fileName,
        ?int $shopId = null,
    ): array {
        $uri = $this->credentials->apiUrl . $path . '?' . http_build_query($this->shopCommonParams($path, $shopId));
        $request = $this->buildMultipartRequest($uri, $fields, [
            ['name' => $fileFieldName, 'contents' => $fileContents, 'filename' => $fileName],
        ]);

        return $this->decodeJson($this->sendRequest($request));
    }

    /**
     * Upload one or more files to a Public API endpoint as
     * `multipart/form-data` (no access token required) — used by MediaSpace's
     * image/video-part upload endpoints, which sign at the Public level
     * despite being tied to a shop's product/video content.
     *
     * @param array<string, scalar> $fields
     * @param array<int, array{name: string, contents: mixed, filename?: string}> $fileParts
     * @return array<string, mixed>
     */
    public function publicUpload(string $path, array $fields, array $fileParts): array
    {
        $timestamp = time();
        $baseString = $this->signer->publicBaseString($this->credentials->partnerId, $path, $timestamp);

        $common = [
            'partner_id' => $this->credentials->partnerId,
            'timestamp' => $timestamp,
            'sign' => $this->signer->sign($this->credentials->partnerKey, $baseString),
        ];

        $uri = $this->credentials->apiUrl . $path . '?' . http_build_query($common);
        $request = $this->buildMultipartRequest($uri, $fields, $fileParts);

        return $this->decodeJson($this->sendRequest($request));
    }

    /**
     * Call a Shop API endpoint (GET) whose response is a raw file body rather
     * than JSON (Shopee still returns a JSON `error` body on failure, which
     * is detected and raised as an {@see ApiException} same as any other call).
     *
     * @param array<string, mixed> $params
     */
    public function shopDownload(string $path, array $params = [], ?int $shopId = null): string
    {
        $common = $this->shopCommonParams($path, $shopId);

        $uri = $this->credentials->apiUrl . $path . '?' . http_build_query([...$common, ...$params]);
        $request = $this->requestFactory->createRequest('GET', $uri);

        $response = $this->sendRequest($request);
        $body = (string) $response->getBody();
        $this->throwIfErrorBody($body, $response->getStatusCode());

        return $body;
    }

    /**
     * Call a Shop API endpoint (POST with a JSON body) whose response is a
     * raw file body on success but a JSON `error` body on failure — used by
     * endpoints such as Logistics' shipping-document downloads, which unlike
     * {@see shopDownload()} require request parameters in the body, not the query.
     *
     * @param array<string, mixed> $params
     */
    public function shopDownloadPost(string $path, array $params = [], ?int $shopId = null): string
    {
        $common = $this->shopCommonParams($path, $shopId);

        $uri = $this->credentials->apiUrl . $path . '?' . http_build_query($common);
        $request = $this->requestFactory->createRequest('POST', $uri)
            ->withHeader('Content-Type', 'application/json')
            ->withBody($this->streamFactory->createStream(json_encode($params, JSON_THROW_ON_ERROR)));

        $response = $this->sendRequest($request);
        $body = (string) $response->getBody();
        $this->throwIfErrorBody($body, $response->getStatusCode());

        return $body;
    }

    /**
     * @param array<string, scalar> $fields
     * @param array<int, array{name: string, contents: mixed, filename?: string}> $fileParts
     */
    private function buildMultipartRequest(string $uri, array $fields, array $fileParts): RequestInterface
    {
        $parts = [];
        foreach ($fields as $name => $value) {
            $parts[] = ['name' => $name, 'contents' => (string) $value];
        }

        array_push($parts, ...$fileParts);

        $multipart = new MultipartStream($parts);

        return $this->requestFactory->createRequest('POST', $uri)
            ->withHeader('Content-Type', 'multipart/form-data; boundary=' . $multipart->getBoundary())
            ->withBody($multipart);
    }

    private function requireAccessToken(): AccessToken
    {
        if ($this->accessToken === null) {
            throw new \LogicException('An access token is required for this call. Provide one via Client::withAccessToken().');
        }

        return $this->accessToken;
    }

    /**
     * @return array<string, mixed>
     */
    private function shopCommonParams(string $path, ?int $shopId): array
    {
        $accessToken = $this->requireAccessToken();
        $shopId ??= $accessToken->shopId;

        if ($shopId === null) {
            throw new \InvalidArgumentException('A shop_id is required to call a Shop API and none was provided or found on the access token.');
        }

        $timestamp = time();
        $baseString = $this->signer->shopBaseString(
            $this->credentials->partnerId,
            $path,
            $timestamp,
            $accessToken->accessToken,
            $shopId,
        );

        return [
            'partner_id' => $this->credentials->partnerId,
            'timestamp' => $timestamp,
            'sign' => $this->signer->sign($this->credentials->partnerKey, $baseString),
            'access_token' => $accessToken->accessToken,
            'shop_id' => $shopId,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function userCommonParams(string $path, ?int $userId): array
    {
        $accessToken = $this->requireAccessToken();
        $userId ??= $accessToken->userId;

        if ($userId === null) {
            throw new \InvalidArgumentException('A user_id is required to call a User API and none was provided or found on the access token.');
        }

        $timestamp = time();
        $baseString = $this->signer->userBaseString(
            $this->credentials->partnerId,
            $path,
            $timestamp,
            $accessToken->accessToken,
            $userId,
        );

        return [
            'partner_id' => $this->credentials->partnerId,
            'timestamp' => $timestamp,
            'sign' => $this->signer->sign($this->credentials->partnerKey, $baseString),
            'access_token' => $accessToken->accessToken,
            'user_id' => $userId,
        ];
    }

    /**
     * @param array<string, mixed> $commonParams
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function send(string $method, string $path, array $commonParams, array $params): array
    {
        $method = strtoupper($method);
        $query = $commonParams;
        $body = null;

        if ($method === 'GET') {
            $query = [...$query, ...$params];
        } else {
            $body = $params;
        }

        $uri = $this->credentials->apiUrl . $path . '?' . http_build_query($query);

        $request = $this->requestFactory->createRequest($method, $uri)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json');

        if ($body !== null) {
            $request = $request->withBody(
                $this->streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR)),
            );
        }

        return $this->decodeJson($this->sendRequest($request));
    }

    private function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->httpClient->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new TransportException('Failed to reach the Shopee Open Platform: ' . $e->getMessage(), previous: $e);
        }
    }

    /**
     * Raises an {@see ApiException} if a raw response body turns out to
     * actually be a Shopee JSON error object, rather than the expected file
     * bytes. Used by the `shopDownload*()` methods, whose success response
     * is never JSON, so any JSON body found here can only mean an error.
     */
    private function throwIfErrorBody(string $body, int $status): void
    {
        $decoded = json_decode($body, true);

        if (is_array($decoded) && array_key_exists('request_id', $decoded) && !empty($decoded['error'])) {
            throw new ApiException(
                errorCode: (string) $decoded['error'],
                shopeeMessage: (string) ($decoded['message'] ?? ''),
                requestId: (string) $decoded['request_id'],
                httpStatus: $status,
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJson(ResponseInterface $response): array
    {
        $status = $response->getStatusCode();
        $decoded = json_decode((string) $response->getBody(), true);

        if (!is_array($decoded)) {
            throw new TransportException(sprintf('Received a non-JSON response from the Shopee Open Platform (HTTP %d).', $status));
        }

        if (!empty($decoded['error'])) {
            throw new ApiException(
                errorCode: (string) $decoded['error'],
                shopeeMessage: (string) ($decoded['message'] ?? ''),
                requestId: isset($decoded['request_id']) ? (string) $decoded['request_id'] : null,
                httpStatus: $status,
            );
        }

        return $decoded;
    }
}
