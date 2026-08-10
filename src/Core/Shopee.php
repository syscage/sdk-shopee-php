<?php

declare(strict_types=1);

namespace Syscage\Sdk\Shopee\Core;

use Syscage\Sdk\Shopee\Core\Http\Api\AccountHealth;
use Syscage\Sdk\Shopee\Core\Http\Api\AddOnDeal;
use Syscage\Sdk\Shopee\Core\Http\Api\Ads;
use Syscage\Sdk\Shopee\Core\Http\Api\Ams;
use Syscage\Sdk\Shopee\Core\Http\Api\BrandPortal;
use Syscage\Sdk\Shopee\Core\Http\Api\BundleDeal;
use Syscage\Sdk\Shopee\Core\Http\Api\Discount;
use Syscage\Sdk\Shopee\Core\Http\Api\Fbs;
use Syscage\Sdk\Shopee\Core\Http\Api\FirstMile;
use Syscage\Sdk\Shopee\Core\Http\Api\FollowPrize;
use Syscage\Sdk\Shopee\Core\Http\Api\GlobalProduct;
use Syscage\Sdk\Shopee\Core\Http\Api\Livestream;
use Syscage\Sdk\Shopee\Core\Http\Api\Logistics;
use Syscage\Sdk\Shopee\Core\Http\Api\Media;
use Syscage\Sdk\Shopee\Core\Http\Api\MediaSpace;
use Syscage\Sdk\Shopee\Core\Http\Api\Merchant;
use Syscage\Sdk\Shopee\Core\Http\Api\Order;
use Syscage\Sdk\Shopee\Core\Http\Api\Payment;
use Syscage\Sdk\Shopee\Core\Http\Api\Product;
use Syscage\Sdk\Shopee\Core\Http\Api\PublicApi;
use Syscage\Sdk\Shopee\Core\Http\Api\Push;
use Syscage\Sdk\Shopee\Core\Http\Api\Returns;
use Syscage\Sdk\Shopee\Core\Http\Api\Sbs;
use Syscage\Sdk\Shopee\Core\Http\Api\Shop;
use Syscage\Sdk\Shopee\Core\Http\Api\ShopCategory;
use Syscage\Sdk\Shopee\Core\Http\Api\ShopFlashSale;
use Syscage\Sdk\Shopee\Core\Http\Api\TopPicks;
use Syscage\Sdk\Shopee\Core\Http\Api\Video;
use Syscage\Sdk\Shopee\Core\Http\Api\Voucher;
use Syscage\Sdk\Shopee\Core\Http\Auth\AccessToken;
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;
use Syscage\Sdk\Shopee\Core\Http\Auth\OAuth;
use Syscage\Sdk\Shopee\Core\Http\Client;

/**
 * SDK entry point.
 *
 * ```php
 * $credentials = new Credentials($partnerId, $partnerKey, $apiUrl, $authUrl);
 * $shopee = new Shopee($credentials);
 *
 * $authUrl = $shopee->oauth()->getAuthorizationUrl($redirectUri);
 * $token = $shopee->oauth()->getAccessTokenForShop($code, $shopId);
 *
 * $shopee = $shopee->withAccessToken($token);
 * $info = $shopee->shop()->getShopInfo();
 * ```
 */
final class Shopee
{
    private Client $client;
    private ?OAuth $oauth = null;
    private ?Shop $shop = null;
    private ?Product $product = null;
    private ?Order $order = null;
    private ?Logistics $logistics = null;
    private ?Merchant $merchant = null;
    private ?GlobalProduct $globalProduct = null;
    private ?MediaSpace $mediaSpace = null;
    private ?Payment $payment = null;
    private ?Discount $discount = null;
    private ?BundleDeal $bundleDeal = null;
    private ?AddOnDeal $addOnDeal = null;
    private ?Voucher $voucher = null;
    private ?FollowPrize $followPrize = null;
    private ?Media $media = null;
    private ?Push $push = null;
    private ?Fbs $fbs = null;
    private ?TopPicks $topPicks = null;
    private ?Sbs $sbs = null;
    private ?AccountHealth $accountHealth = null;
    private ?PublicApi $publicApi = null;
    private ?ShopCategory $shopCategory = null;
    private ?BrandPortal $brandPortal = null;
    private ?ShopFlashSale $shopFlashSale = null;
    private ?Returns $returns = null;
    private ?Video $video = null;
    private ?FirstMile $firstMile = null;
    private ?Ads $ads = null;
    private ?Livestream $livestream = null;
    private ?Ams $ams = null;

    public function __construct(
        private readonly Credentials $credentials,
        ?AccessToken $accessToken = null,
        ?Client $client = null,
    ) {
        $this->client = $client ?? new Client($this->credentials, $accessToken);
    }

    /**
     * Return a new instance scoped to the given shop/merchant access token.
     */
    public function withAccessToken(AccessToken $accessToken): self
    {
        $clone = clone $this;
        $clone->client = $this->client->withAccessToken($accessToken);
        $clone->oauth = null;
        $clone->shop = null;
        $clone->product = null;
        $clone->order = null;
        $clone->logistics = null;
        $clone->merchant = null;
        $clone->globalProduct = null;
        $clone->mediaSpace = null;
        $clone->payment = null;
        $clone->discount = null;
        $clone->bundleDeal = null;
        $clone->addOnDeal = null;
        $clone->voucher = null;
        $clone->followPrize = null;
        $clone->media = null;
        $clone->push = null;
        $clone->fbs = null;
        $clone->topPicks = null;
        $clone->sbs = null;
        $clone->accountHealth = null;
        $clone->publicApi = null;
        $clone->shopCategory = null;
        $clone->brandPortal = null;
        $clone->shopFlashSale = null;
        $clone->returns = null;
        $clone->video = null;
        $clone->firstMile = null;
        $clone->ads = null;
        $clone->livestream = null;
        $clone->ams = null;

        return $clone;
    }

    public function getAccessToken(): ?AccessToken
    {
        return $this->client->getAccessToken();
    }

    public function oauth(): OAuth
    {
        return $this->oauth ??= new OAuth($this->credentials, $this->client);
    }

    public function shop(): Shop
    {
        return $this->shop ??= new Shop($this->client);
    }

    public function product(): Product
    {
        return $this->product ??= new Product($this->client);
    }

    public function order(): Order
    {
        return $this->order ??= new Order($this->client);
    }

    public function logistics(): Logistics
    {
        return $this->logistics ??= new Logistics($this->client);
    }

    public function merchant(): Merchant
    {
        return $this->merchant ??= new Merchant($this->client);
    }

    public function globalProduct(): GlobalProduct
    {
        return $this->globalProduct ??= new GlobalProduct($this->client);
    }

    public function mediaSpace(): MediaSpace
    {
        return $this->mediaSpace ??= new MediaSpace($this->client);
    }

    public function payment(): Payment
    {
        return $this->payment ??= new Payment($this->client);
    }

    public function discount(): Discount
    {
        return $this->discount ??= new Discount($this->client);
    }

    public function bundleDeal(): BundleDeal
    {
        return $this->bundleDeal ??= new BundleDeal($this->client);
    }

    public function addOnDeal(): AddOnDeal
    {
        return $this->addOnDeal ??= new AddOnDeal($this->client);
    }

    public function voucher(): Voucher
    {
        return $this->voucher ??= new Voucher($this->client);
    }

    public function followPrize(): FollowPrize
    {
        return $this->followPrize ??= new FollowPrize($this->client);
    }

    public function media(): Media
    {
        return $this->media ??= new Media($this->client);
    }

    public function push(): Push
    {
        return $this->push ??= new Push($this->client);
    }

    public function fbs(): Fbs
    {
        return $this->fbs ??= new Fbs($this->client);
    }

    public function topPicks(): TopPicks
    {
        return $this->topPicks ??= new TopPicks($this->client);
    }

    public function sbs(): Sbs
    {
        return $this->sbs ??= new Sbs($this->client);
    }

    public function accountHealth(): AccountHealth
    {
        return $this->accountHealth ??= new AccountHealth($this->client);
    }

    /**
     * Named `public()` (matching Shopee's own domain name), even though the
     * underlying class is {@see PublicApi} — `public` is a reserved PHP
     * keyword and cannot be a class name, but it is a valid method name.
     */
    public function public(): PublicApi
    {
        return $this->publicApi ??= new PublicApi($this->client);
    }

    public function shopCategory(): ShopCategory
    {
        return $this->shopCategory ??= new ShopCategory($this->client);
    }

    public function brandPortal(): BrandPortal
    {
        return $this->brandPortal ??= new BrandPortal($this->client);
    }

    public function shopFlashSale(): ShopFlashSale
    {
        return $this->shopFlashSale ??= new ShopFlashSale($this->client);
    }

    public function returns(): Returns
    {
        return $this->returns ??= new Returns($this->client);
    }

    public function video(): Video
    {
        return $this->video ??= new Video($this->client);
    }

    public function firstMile(): FirstMile
    {
        return $this->firstMile ??= new FirstMile($this->client);
    }

    public function ads(): Ads
    {
        return $this->ads ??= new Ads($this->client);
    }

    public function livestream(): Livestream
    {
        return $this->livestream ??= new Livestream($this->client);
    }

    public function ams(): Ams
    {
        return $this->ams ??= new Ams($this->client);
    }
}
