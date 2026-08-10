<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Support;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * Builds a {@see Client} backed by a Guzzle MockHandler, so tests can assert
 * on the outgoing request without any network access.
 */
trait InteractsWithMockHttp
{
    /** @var array<int, array{request: RequestInterface}> */
    private array $requestHistory = [];

    private function testCredentials(): Credentials
    {
        return new Credentials(
            partnerId: 2001887,
            partnerKey: 'test-partner-key',
            apiUrl: 'https://openplatform.sandbox.test-stable.shopee.sg',
            authUrl: 'https://open.test-stable.shopee.com',
        );
    }

    /**
     * @param array<int, Response> $responses
     */
    private function makeClient(array $responses, ?Credentials $credentials = null): Client
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($this->requestHistory));

        $guzzle = new GuzzleClient(['handler' => $stack]);

        return new Client($credentials ?? $this->testCredentials(), httpClient: $guzzle);
    }

    private function lastRequest(): RequestInterface
    {
        return $this->requestHistory[array_key_last($this->requestHistory)]['request'];
    }

    /** @return array<string, mixed> */
    private function lastRequestBodyAsArray(): array
    {
        return json_decode((string) $this->lastRequest()->getBody(), true);
    }

    /** @return array<string, string> */
    private function lastRequestQuery(): array
    {
        parse_str($this->lastRequest()->getUri()->getQuery(), $query);

        return $query;
    }
}
