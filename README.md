# bhekor/laravel-opay

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bhekor/laravel-opay.svg)](https://packagist.org/packages/bhekor/laravel-opay)
[![Total Downloads](https://img.shields.io/packagist/dt/bhekor/laravel-opay.svg)](https://packagist.org/packages/bhekor/laravel-opay)
[![License](https://img.shields.io/packagist/l/bhekor/laravel-opay.svg)](LICENSE.md)
[![GitHub Stars](https://img.shields.io/github/stars/bhekor/laravel-opay.svg?style=social)](https://github.com/bhekor/laravel-opay)

Reusable Laravel package for [OPay Checkout](https://documentation.opaycheckout.com/) APIs.

## Features

- Cashier create / status (preferred wallet flow: `OpayWalletNg` + `evokeOpay`)
- Direct payment create (including `OpayWalletNgQR`)
- Refund create
- HMAC-SHA512 request signing
- HMAC-SHA3-512 callback verification
- Sandbox and live environments

This package does **not** contain host wallet, customer, or transaction business logic.

## Installation

```bash
composer require bhekor/laravel-opay
```

Laravel discovers the service provider and `Opay` facade automatically. Publishing the config is optional; it creates `config/opay.php` so you can override package defaults.

```bash
php artisan vendor:publish --tag=opay-config
```

## Configuration

| Env | Description |
|---|---|
| `OPAY_BASE_URL` | API host — sandbox `https://testapi.opaycheckout.com` or live `https://liveapi.opaycheckout.com` |
| `OPAY_MERCHANT_ID` | Merchant ID |
| `OPAY_PUBLIC_KEY` | Public key (cashier create) |
| `OPAY_SECRET_KEY` | Secret key (status / refund / callbacks) |
| `OPAY_DEFAULT_PAY_METHOD` | Default `OpayWalletNg` |
| `OPAY_EVOKE_OPAY` | Default `true` |
| `OPAY_TIMEOUT` | HTTP request timeout in seconds — default `60` |
| `OPAY_COUNTRY` | Default country — `NG` |
| `OPAY_CURRENCY` | Default currency — `NGN` |
| `OPAY_EXPIRE_AT_MINUTES` | Cashier session expiry — default `30` |

Environment is implied by the base URL and which keys you configure (no `OPAY_ENV` flag).

## Usage

```php
use Bhekor\LaravelOpay\Facades\Opay;
use Bhekor\LaravelOpay\Support\Money;

$result = Opay::cashier()->create([
    'country' => 'NG',
    'reference' => $merchantReference,
    'amount' => [
        'total' => Money::toMinor(1500),
        'currency' => 'NGN',
    ],
    'returnUrl' => $returnUrl,
    'callbackUrl' => $webhookUrl,
    'cancelUrl' => $cancelUrl,
    'payMethod' => 'OpayWalletNg',
    'evokeOpay' => true,
    'customerVisitSource' => 'BROWSER', // IOS | ANDROID | BROWSER
    'product' => [
        'name' => 'Wallet top-up',
        'description' => 'Utilify Networks',
    ],
]);

$cashierUrl = $result->cashierUrl;

$status = Opay::cashier()->status([
    'country' => 'NG',
    'reference' => $merchantReference,
]);

$callback = Opay::webhooks()->parse($request->all());
```

See [MERCHANT_ONBOARDING.md](MERCHANT_ONBOARDING.md) for go-live checklist.

## Tests

```bash
composer install
composer test
```

## License

MIT
