<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Exceptions;

/**
 * Raised when the HTTP call to the Shopee Open Platform itself fails
 * (network failure, timeout, or a non-JSON response body).
 */
final class TransportException extends ShopeeException
{
}
