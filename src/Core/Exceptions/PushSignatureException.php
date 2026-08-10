<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Exceptions;

/**
 * Raised when a push notification's `Authorization` header does not match
 * the signature computed from the callback URL, raw body, and partner key.
 */
final class PushSignatureException extends ShopeeException
{
}
