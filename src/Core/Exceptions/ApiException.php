<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core\Exceptions;

/**
 * Raised when the Shopee Open Platform responds with a non-empty `error` field.
 */
final class ApiException extends ShopeeException
{
    public function __construct(
        private readonly string $errorCode,
        private readonly string $shopeeMessage,
        private readonly ?string $requestId,
        private readonly int $httpStatus,
    ) {
        parent::__construct(sprintf(
            'Shopee API error [%s]: %s',
            $this->errorCode,
            $this->shopeeMessage !== '' ? $this->shopeeMessage : $this->errorCode,
        ));
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getShopeeMessage(): string
    {
        return $this->shopeeMessage;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }
}
