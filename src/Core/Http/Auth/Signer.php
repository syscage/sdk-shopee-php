<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Auth;

/**
 * Builds and computes the HMAC-SHA256 request signature required by every
 * Shopee Open Platform v2 API call.
 *
 * @see .shopee-docs/Developer Guide/Getting Started/api_calls.md
 */
final class Signer
{
    public function __construct(private readonly string $algorithm = 'sha256')
    {
    }

    public function sign(string $partnerKey, string $baseString): string
    {
        return hash_hmac($this->algorithm, $baseString, $partnerKey);
    }

    /** Base string for Public APIs: partner_id, api path, timestamp. */
    public function publicBaseString(int $partnerId, string $path, int $timestamp): string
    {
        return $partnerId . $path . $timestamp;
    }

    /** Base string for Shop APIs: partner_id, api path, timestamp, access_token, shop_id. */
    public function shopBaseString(int $partnerId, string $path, int $timestamp, string $accessToken, int $shopId): string
    {
        return $partnerId . $path . $timestamp . $accessToken . $shopId;
    }

    /** Base string for Merchant APIs: partner_id, api path, timestamp, access_token, merchant_id. */
    public function merchantBaseString(int $partnerId, string $path, int $timestamp, string $accessToken, int $merchantId): string
    {
        return $partnerId . $path . $timestamp . $accessToken . $merchantId;
    }

    /** Base string for Principal APIs (e.g. BrandPortal): partner_id, api path, timestamp, access_token, principal_id. */
    public function principalBaseString(int $partnerId, string $path, int $timestamp, string $accessToken, int $principalId): string
    {
        return $partnerId . $path . $timestamp . $accessToken . $principalId;
    }

    /** Base string for User APIs (e.g. Video): partner_id, api path, timestamp, access_token, user_id. */
    public function userBaseString(int $partnerId, string $path, int $timestamp, string $accessToken, int $userId): string
    {
        return $partnerId . $path . $timestamp . $accessToken . $userId;
    }
}
