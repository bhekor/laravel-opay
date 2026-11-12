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

## Installation

```bash
composer require bhekor/laravel-opay
```

Laravel discovers the service provider and `Opay` facade automatically. Publishing the config is optional; it creates `config/opay.php` so you can override package defaults.

```bash
php artisan vendor:publish --tag=opay-config
```

### Local path repository

```json
{
  "repositories": [
    { "type": "path", "url": "../packages/laravel-opay", "options": { "symlink": true } }
  ],
  "require": {
    "bhekor/laravel-opay": "dev-main"
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

## Publishing updates to Packagist

The package source of truth is [bhekor/laravel-opay on GitHub](https://github.com/bhekor/laravel-opay). To publish it on Packagist:

1. Push this repository to GitHub and make sure the default branch contains `composer.json`, this README, and `LICENSE.md`.
2. Sign in to [Packagist](https://packagist.org/) and create an account if needed.
3. Choose **Submit** and enter `https://github.com/bhekor/laravel-opay`.
4. If prompted, configure the Packagist GitHub Service Hook/webhook for the repository so Packagist refreshes automatically after pushes and tags.
5. Create and push a version tag for each release. Use semantic versions such as `v1.0.0`; publish a GitHub release for the same tag. Packagist imports the tag as a stable Composer version.

For later releases, update the package code and changelog/README as needed, commit and push, then create and push the next version tag. Do not add a `version` field to `composer.json`; Composer derives releases from VCS tags. Before tagging, check the package metadata with:

```bash
composer validate --strict
```

Consumers can then install a stable release with `composer require bhekor/laravel-opay` or pin a release with `composer require bhekor/laravel-opay:^1.0`.

### Before the first public release

- Confirm the GitHub repository is public and the repository URL, owner, and package name are correct.
- Choose the initial release version based on the package's API stability (for example, `v0.1.0` while the API is still evolving).
- Review the included license, README links, and credentials/examples; never publish real merchant keys.
- Ensure the version tag points to the commit you intend to release. Tags should be immutable; make a new version for corrections.

## Tests

```bash
composer install
composer test
```

## License

MIT
