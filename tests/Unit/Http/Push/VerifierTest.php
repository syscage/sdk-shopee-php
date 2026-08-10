<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Push;

use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Push\Verifier;

/**
 * Base string verified against the documented shape in:
 * .shopee-docs/Developer Guide/Getting Started/push_mechanism_notifications.md
 */
final class VerifierTest extends TestCase
{
    public function testSignConcatenatesUrlAndBodyWithPipe(): void
    {
        $verifier = new Verifier();

        $signature = $verifier->sign(
            'http://www.example.com/example/uri',
            '{"shop_id": 123, "code": 1, "success": 1}',
            'test-partner-key',
        );

        $this->assertSame(
            hash_hmac('sha256', 'http://www.example.com/example/uri|{"shop_id": 123, "code": 1, "success": 1}', 'test-partner-key'),
            $signature,
        );
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $signature);
    }

    public function testVerifyReturnsTrueForMatchingSignature(): void
    {
        $verifier = new Verifier();
        $url = 'https://example.test/push';
        $body = '{"code":3,"timestamp":1660123127,"data":{}}';
        $key = 'test-partner-key';

        $signature = $verifier->sign($url, $body, $key);

        $this->assertTrue($verifier->verify($url, $body, $key, $signature));
    }

    public function testVerifyReturnsFalseForMismatchedSignature(): void
    {
        $verifier = new Verifier();

        $this->assertFalse($verifier->verify(
            'https://example.test/push',
            '{"code":3,"timestamp":1660123127,"data":{}}',
            'test-partner-key',
            'not-the-right-signature',
        ));
    }

    public function testVerifyReturnsFalseWhenBodyDiffersEvenWithSameKey(): void
    {
        $verifier = new Verifier();
        $url = 'https://example.test/push';
        $key = 'test-partner-key';

        $signature = $verifier->sign($url, '{"code":3}', $key);

        $this->assertFalse($verifier->verify($url, '{"code":4}', $key, $signature));
    }
}
