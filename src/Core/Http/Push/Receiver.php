<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Http\Push;

use Psr\Http\Message\ServerRequestInterface;
use Syscage\Sdk\Shopee\Core\Exceptions\PushPayloadException;
use Syscage\Sdk\Shopee\Core\Exceptions\PushSignatureException;
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;

/**
 * Entry point for receiving Shopee push notifications (webhooks) — the
 * receiving counterpart to `Http\Api\Push`'s config endpoints.
 *
 * ```php
 * $receiver = new Receiver($credentials);
 * $event = $receiver->receive($fullCallbackUrl, $rawRequestBody, $authorizationHeader);
 *
 * match ($event->eventCode()) {
 *     PushEventCode::OrderStatus => handleOrderStatus($event),
 *     default => null,
 * };
 * ```
 *
 * `$url` must be the exact URL Shopee was configured to call (via
 * `v2.push.set_app_push_config` or the Console) — the signature base
 * string is `url|raw_body`, so a mismatched URL (wrong scheme, trailing
 * slash, query string) breaks verification even with a correct partner key.
 *
 * @see .shopee-docs/Developer Guide/Getting Started/push_mechanism_notifications.md
 * @see .docs/push/README.md
 */
final class Receiver
{
    public function __construct(
        private readonly Credentials $credentials,
        private readonly Verifier $verifier = new Verifier(),
    ) {
    }

    /**
     * @throws PushSignatureException if the Authorization header doesn't match.
     * @throws PushPayloadException if the body isn't a valid push payload.
     */
    public function receive(string $url, string $rawBody, string $authorizationHeader): PushEvent
    {
        if (!$this->verifier->verify($url, $rawBody, $this->credentials->partnerKey, $authorizationHeader)) {
            throw new PushSignatureException('Push notification signature does not match.');
        }

        return PushEvent::fromJson($rawBody);
    }

    /**
     * Convenience for PSR-7/PSR-15 consumers — extracts the URL, body, and
     * `Authorization` header from a {@see ServerRequestInterface}.
     *
     * @throws PushSignatureException if the Authorization header doesn't match.
     * @throws PushPayloadException if the body isn't a valid push payload.
     */
    public function receiveFromServerRequest(ServerRequestInterface $request): PushEvent
    {
        return $this->receive(
            (string) $request->getUri(),
            (string) $request->getBody(),
            $request->getHeaderLine('Authorization'),
        );
    }
}
