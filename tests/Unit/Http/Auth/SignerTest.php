<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Tests\Unit\Http\Auth;

use PHPUnit\Framework\TestCase;
use Syscage\Sdk\Shopee\Core\Http\Auth\Signer;

/**
 * Base strings are verified against the worked examples in:
 * .shopee-docs/Developer Guide/Getting Started/api_calls.md
 */
final class SignerTest extends TestCase
{
    public function testPublicBaseStringMatchesDocumentedExample(): void
    {
        $signer = new Signer();

        $baseString = $signer->publicBaseString(2001887, '/api/v2/public/get_shops_by_partner', 1655714431);

        $this->assertSame('2001887/api/v2/public/get_shops_by_partner1655714431', $baseString);
    }

    public function testShopBaseStringMatchesDocumentedExample(): void
    {
        $signer = new Signer();

        $baseString = $signer->shopBaseString(
            2001887,
            '/api/v2/shop/get_shop_info',
            1655714431,
            '59777174636562737266615546704c6d',
            14701711,
        );

        $this->assertSame(
            '2001887/api/v2/shop/get_shop_info165571443159777174636562737266615546704c6d14701711',
            $baseString,
        );
    }

    public function testMerchantBaseStringMatchesDocumentedExample(): void
    {
        $signer = new Signer();

        $baseString = $signer->merchantBaseString(
            2001887,
            '/api/v2/global_product/get_category',
            1655714431,
            '09777174636962737266615546704c6d',
            1000000,
        );

        $this->assertSame(
            '2001887/api/v2/global_product/get_category165571443109777174636962737266615546704c6d1000000',
            $baseString,
        );
    }

    public function testPrincipalBaseStringConcatenatesInDocumentedOrder(): void
    {
        $signer = new Signer();

        $baseString = $signer->principalBaseString(
            2001887,
            '/api/v2/principal/get_shop_sales_performance_detail',
            1655714431,
            '09777174636962737266615546704c6d',
            55001,
        );

        $this->assertSame(
            '2001887/api/v2/principal/get_shop_sales_performance_detail165571443109777174636962737266615546704c6d55001',
            $baseString,
        );
    }

    public function testUserBaseStringConcatenatesInDocumentedOrder(): void
    {
        $signer = new Signer();

        $baseString = $signer->userBaseString(
            2001887,
            '/api/v2/video/get_video_list',
            1655714431,
            '09777174636962737266615546704c6d',
            987654,
        );

        $this->assertSame(
            '2001887/api/v2/video/get_video_list165571443109777174636962737266615546704c6d987654',
            $baseString,
        );
    }

    public function testSignUsesHmacSha256HexDigest(): void
    {
        $signer = new Signer();

        $sign = $signer->sign('test-partner-key', 'some-base-string');

        $this->assertSame(hash_hmac('sha256', 'some-base-string', 'test-partner-key'), $sign);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $sign);
    }
}
