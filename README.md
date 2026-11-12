# bhekor/laravel-opay

Reusable Laravel package for [OPay Checkout](https://documentation.opaycheckout.com/) APIs.

## Features

- Cashier create / status (preferred wallet flow: `OpayWalletNg` + `evokeOpay`)
- Direct payment create (including `OpayWalletNgQR`)
- Refund create
- HMAC-SHA512 request signing
- HMAC-SHA3-512 callback verification
- Sandbox and live environments

This package does **not** contain host wallet, customer, or transaction business logic.

## Installation (path / Packagist)

```bash
composer require bhekor/laravel-opay
php artisan vendor:publish --tag=opay-config
```

### Local path repository

```json
{
  "repositories": [
    { "type": "path", "url": "../packages/laravel-opay", "options": { "symlink": true } }
  ],
  "require": {
    "bhekor/laravel-opay": "*@dev"
  }
}
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
