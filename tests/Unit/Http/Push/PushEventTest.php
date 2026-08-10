<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Push;

use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Exceptions\PushPayloadException;
use Syscage\Sdk\Shopee\Core\Http\Push\PushEvent;
use Syscage\Sdk\Shopee\Core\Http\Push\PushEventCode;

final class PushEventTest extends TestCase
{
    public function testFromJsonParsesCodeTimestampAndData(): void
    {
        $event = PushEvent::fromJson(json_encode([
            'shop_id' => 727720655,
            'code' => 3,
            'timestamp' => 1660123127,
            'data' => ['ordersn' => '220810QSK8S7BX', 'status' => 'PROCESSED'],
        ]));

        $this->assertSame(3, $event->code);
        $this->assertSame(1660123127, $event->timestamp);
        $this->assertSame('220810QSK8S7BX', $event->data['ordersn']);
        $this->assertSame(727720655, $event->raw['shop_id']);
    }

    public function testFromJsonDefaultsDataToEmptyArrayWhenAbsent(): void
    {
        $event = PushEvent::fromJson(json_encode(['code' => 5, 'timestamp' => 1]));

        $this->assertSame([], $event->data);
    }

    public function testFromJsonThrowsOnInvalidJson(): void
    {
        $this->expectException(PushPayloadException::class);

        PushEvent::fromJson('not json');
    }

    public function testFromJsonThrowsWhenCodeMissing(): void
    {
        $this->expectException(PushPayloadException::class);

        PushEvent::fromJson(json_encode(['timestamp' => 1]));
    }

    public function testFromJsonThrowsWhenTimestampMissing(): void
    {
        $this->expectException(PushPayloadException::class);

        PushEvent::fromJson(json_encode(['code' => 3]));
    }

    public function testEventCodeReturnsMatchingEnumCase(): void
    {
        $event = PushEvent::fromJson(json_encode(['code' => 3, 'timestamp' => 1]));

        $this->assertSame(PushEventCode::OrderStatus, $event->eventCode());
    }

    public function testEventCodeReturnsNullForUnknownCode(): void
    {
        $event = PushEvent::fromJson(json_encode(['code' => 999, 'timestamp' => 1]));

        $this->assertNull($event->eventCode());
        $this->assertSame(999, $event->code);
    }

    public function testShopIdReadsTopLevelFirst(): void
    {
        // order_status_push shape: shop_id is top-level, not inside data.
        $event = PushEvent::fromJson(json_encode([
            'shop_id' => 727720655,
            'code' => 3,
            'timestamp' => 1,
            'data' => ['shop_id' => 999],
        ]));

        $this->assertSame(727720655, $event->shopId());
    }

    public function testShopIdFallsBackToDataField(): void
    {
        // shop_authorization_push shape: shop_id only exists inside data.
        $event = PushEvent::fromJson(json_encode([
            'partner_id' => 2000002,
            'code' => 1,
            'timestamp' => 1,
            'data' => ['shop_id' => 60011111, 'success' => 1],
        ]));

        $this->assertSame(60011111, $event->shopId());
        $this->assertSame(2000002, $event->partnerId());
    }

    public function testShopIdReturnsNullWhenAbsentEntirely(): void
    {
        $event = PushEvent::fromJson(json_encode([
            'code' => 12,
            'timestamp' => 1,
            'data' => ['expire_before' => 1],
        ]));

        $this->assertNull($event->shopId());
    }

    public function testSupplierIdAccessor(): void
    {
        $event = PushEvent::fromJson(json_encode([
            'supplier_id' => 42,
            'code' => 21,
            'timestamp' => 1,
            'data' => ['inbound_id' => 'in1'],
        ]));

        $this->assertSame(42, $event->supplierId());
        $this->assertNull($event->shopId());
    }

    public function testMerchantIdAccessor(): void
    {
        $event = PushEvent::fromJson(json_encode([
            'code' => 1,
            'timestamp' => 1,
            'data' => ['merchant_id' => 600222872],
        ]));

        $this->assertSame(600222872, $event->merchantId());
    }
}
