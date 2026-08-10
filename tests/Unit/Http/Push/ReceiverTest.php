<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Push;

use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Exceptions\PushPayloadException;
use Syscage\Sdk\Shopee\Core\Exceptions\PushSignatureException;
use Syscage\Sdk\Shopee\Core\Http\Push\PushEventCode;
use Syscage\Sdk\Shopee\Core\Http\Push\Receiver;
use Syscage\Sdk\Shopee\Core\Http\Push\Verifier;
use Syscage\Sdk\Shopee\Tests\Support\InteractsWithMockHttp;

final class ReceiverTest extends TestCase
{
    use InteractsWithMockHttp;

    private const URL = 'https://example.test/shopee/push';
    private const BODY = '{"shop_id":727720655,"code":3,"timestamp":1660123127,"data":{"ordersn":"220810QSK8S7BX"}}';

    public function testReceiveReturnsEventWhenSignatureValid(): void
    {
        $receiver = new Receiver($this->testCredentials());
        $signature = (new Verifier())->sign(self::URL, self::BODY, $this->testCredentials()->partnerKey);

        $event = $receiver->receive(self::URL, self::BODY, $signature);

        $this->assertSame(PushEventCode::OrderStatus, $event->eventCode());
        $this->assertSame(727720655, $event->shopId());
        $this->assertSame('220810QSK8S7BX', $event->data['ordersn']);
    }

    public function testReceiveThrowsPushSignatureExceptionWhenSignatureInvalid(): void
    {
        $receiver = new Receiver($this->testCredentials());

        $this->expectException(PushSignatureException::class);

        $receiver->receive(self::URL, self::BODY, 'wrong-signature');
    }

    public function testReceiveThrowsPushPayloadExceptionWhenBodyMalformed(): void
    {
        $receiver = new Receiver($this->testCredentials());
        $body = 'not json';
        $signature = (new Verifier())->sign(self::URL, $body, $this->testCredentials()->partnerKey);

        $this->expectException(PushPayloadException::class);

        $receiver->receive(self::URL, $body, $signature);
    }

    public function testReceiveFromServerRequestExtractsUrlBodyAndHeader(): void
    {
        $receiver = new Receiver($this->testCredentials());
        $signature = (new Verifier())->sign(self::URL, self::BODY, $this->testCredentials()->partnerKey);

        $request = new ServerRequest('POST', self::URL, ['Authorization' => $signature], self::BODY);

        $event = $receiver->receiveFromServerRequest($request);

        $this->assertSame(PushEventCode::OrderStatus, $event->eventCode());
        $this->assertSame(727720655, $event->shopId());
    }

    public function testReceiveFromServerRequestThrowsWhenSignatureInvalid(): void
    {
        $receiver = new Receiver($this->testCredentials());
        $request = new ServerRequest('POST', self::URL, ['Authorization' => 'wrong'], self::BODY);

        $this->expectException(PushSignatureException::class);

        $receiver->receiveFromServerRequest($request);
    }
}
