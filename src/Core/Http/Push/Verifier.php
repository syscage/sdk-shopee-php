<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Push;

/**
 * Computes and verifies the `Authorization` header signature Shopee sends
 * with every push notification: HMAC-SHA256 of `callback_url|raw_body`,
 * keyed by the partner key, hex-encoded.
 *
 * This is a different base-string shape from {@see \Syscage\Sdk\Shopee\Core\Http\Auth\Signer}
 * (outbound API request signing, `partner_id+path+timestamp+...`) — the
 * two are unrelated despite sharing an algorithm, so this stays in its own
 * `Http\Push` namespace rather than becoming another `Signer` method.
 *
 * @see .shopee-docs/Developer Guide/Getting Started/push_mechanism_notifications.md
 */
final class Verifier
{
    public function __construct(private readonly string $algorithm = 'sha256')
    {
    }

    public function sign(string $url, string $rawBody, string $partnerKey): string
    {
        return hash_hmac($this->algorithm, $url . '|' . $rawBody, $partnerKey);
    }

    /**
     * Timing-safe comparison against the `Authorization` header value.
     */
    public function verify(string $url, string $rawBody, string $partnerKey, string $authorizationHeader): bool
    {
        return hash_equals($this->sign($url, $rawBody, $partnerKey), $authorizationHeader);
    }
}
