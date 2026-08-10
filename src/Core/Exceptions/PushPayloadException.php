<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Exceptions;

/**
 * Raised when a push notification's body is not valid JSON or is missing
 * a required envelope field (`code`, `timestamp`).
 */
final class PushPayloadException extends ShopeeException
{
}
