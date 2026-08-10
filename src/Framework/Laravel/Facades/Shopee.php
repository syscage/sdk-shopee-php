<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;

/**
 * Thin facade over the container-bound {@see \Syscage\Sdk\Shopee\Core\Shopee} instance.
 *
 * Contains no business logic — every call is forwarded to the underlying
 * Core SDK instance resolved from the container.
 *
 * @method static \Syscage\Sdk\Shopee\Core\Shopee withAccessToken(AccessToken $accessToken)
 * @method static AccessToken|null getAccessToken()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Auth\OAuth oauth()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Shop shop()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Product product()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Order order()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Logistics logistics()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Merchant merchant()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\GlobalProduct globalProduct()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\MediaSpace mediaSpace()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Payment payment()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Discount discount()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\BundleDeal bundleDeal()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\AddOnDeal addOnDeal()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Voucher voucher()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\FollowPrize followPrize()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Media media()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Push push()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Fbs fbs()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\TopPicks topPicks()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Sbs sbs()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\AccountHealth accountHealth()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\PublicApi public()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\ShopCategory shopCategory()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\BrandPortal brandPortal()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\ShopFlashSale shopFlashSale()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Returns returns()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Video video()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\FirstMile firstMile()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Ads ads()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Livestream livestream()
 * @method static \Syscage\Sdk\Shopee\Core\Http\Api\Ams ams()
 *
 * @see \Syscage\Sdk\Shopee\Core\Shopee
 */
final class Shopee extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Syscage\Sdk\Shopee\Core\Shopee::class;
    }
}
