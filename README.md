# syscage/sdk-shopee-php

A framework-independent PHP SDK for the Shopee Open Platform.

## Key Features

- Full coverage of the Shopee Open Platform API (Product, Order, Logistics,
  Payment, Ads, Livestream, and every other published API domain)
- Partner authentication, request signing, and the OAuth authorization flow
- A clean, typed HTTP client and consistent per-domain API clients
- Push Mechanism support (webhook signature verification and event parsing)
- A framework-independent Core SDK, usable in plain PHP
- Optional Laravel integration (Service Provider + Facade)
- Optional Symfony integration (Bundle + Dependency Injection)

## Requirements

- PHP `^8.1`
- A PSR-18 HTTP client (the SDK depends on `guzzlehttp/guzzle` by default)

## Installation

```bash
composer require syscage/sdk-shopee-php
```

## Configuration

```php
use Syscage\Sdk\Shopee\Core\Http\Auth\Credentials;

$credentials = new Credentials(
    partnerId: 2001887,
    partnerKey: getenv('SHOPEE_PARTNER_KEY'),
    apiUrl: 'https://partner.shopeemobile.com',
    authUrl: 'https://open.shopee.com',
);
```

Never hard-code your Partner Key — load it from the environment or a
secrets manager.

## Usage

```php
use Syscage\Sdk\Shopee\Core\Shopee;

$shopee = new Shopee($credentials);

// 1. Send the seller to this URL to authorize your app.
$authorizationUrl = $shopee->oauth()->getAuthorizationUrl('https://your-app.test/callback');

// 2. Once the seller is redirected back with ?code=...&shop_id=..., exchange it.
$token = $shopee->oauth()->getAccessTokenForShop($code, $shopId);

// 3. Persist $token yourself, then scope the client to it for API calls.
$shopee = $shopee->withAccessToken($token);
$info = $shopee->shop()->getShopInfo();
```

## Frameworks

### Laravel

Laravel integration is included (Service Provider, container bindings, and
a `Shopee` Facade), auto-discovered via Composer.

See the documentation for installation and configuration.

### Symfony

Symfony integration is included (a Bundle and Dependency Injection
extension — no static Facade, by design).

See the documentation for installation and configuration.

## Documentation

For installation, configuration, authentication, the full API reference,
Push Mechanism, Laravel, Symfony, sandbox usage, testing, and advanced
usage:

**[Read the full documentation](https://doc.syscage.com/sdk-shopee-php)**

## Security

Never commit Partner Keys, Access Tokens, Refresh Tokens, or other secrets
to source control.

## License

MIT
