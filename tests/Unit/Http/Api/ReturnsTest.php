<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Api;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Api\Returns;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ReturnsTest extends TestCase
{
    use InteractsWithMockHttp;

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: array<string, mixed>}>
     */
    public static function endpoints(): array
    {
        return [
            'getReturnList' => ['getReturnList', 'GET', '/api/v2/returns/get_return_list', ['page_no' => 1, 'page_size' => 40]],
            'getReturnDetail' => ['getReturnDetail', 'GET', '/api/v2/returns/get_return_detail', ['return_sn' => '2504060QHMFPXW']],
            'confirm' => ['confirm', 'POST', '/api/v2/returns/confirm', ['return_sn' => '2504060QHMFPXW']],
            'dispute' => ['dispute', 'POST', '/api/v2/returns/dispute', ['return_sn' => '2504060QHMFPXW', 'email' => 'seller@example.test', 'dispute_reason_id' => 1]],
            'cancelDispute' => ['cancelDispute', 'POST', '/api/v2/returns/cancel_dispute', ['return_sn' => '2504060QHMFPXW', 'email' => 'seller@example.test']],
            'offer' => ['offer', 'POST', '/api/v2/returns/offer', ['return_sn' => '2504060QHMFPXW', 'proposed_solution' => 'offer_refund']],
            'acceptOffer' => ['acceptOffer', 'POST', '/api/v2/returns/accept_offer', ['return_sn' => '2504060QHMFPXW']],
            'getAvailableSolutions' => ['getAvailableSolutions', 'GET', '/api/v2/returns/get_available_solutions', ['return_sn' => '2504060QHMFPXW']],
            'getReturnDisputeReason' => ['getReturnDisputeReason', 'GET', '/api/v2/returns/get_return_dispute_reason', ['return_sn' => '2504060QHMFPXW']],
        ];
    }

    #[DataProvider('endpoints')]
    public function testEndpointUsesExpectedHttpMethodAndPath(string $method, string $httpMethod, string $path, array $params): void
    {
        $client = $this->makeClient([
            new Response(200, [], json_encode(['error' => '', 'message' => ''])),
        ])->withAccessToken(new AccessToken('token', 'refresh', time() + 3600, shopId: 14701711));

        (new Returns($client))->{$method}($params);

        $this->assertSame($httpMethod, $this->lastRequest()->getMethod());
        $this->assertSame($path, $this->lastRequest()->getUri()->getPath());
    }

    public function testSubAccessorsAreCached(): void
    {
        $returns = new Returns($this->makeClient([]));

        $this->assertSame($returns->proof(), $returns->proof());
        $this->assertSame($returns->shipping(), $returns->shipping());
    }
}
