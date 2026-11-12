# Changelog

Notable changes to `bhekor/laravel-opay` are documented here.

## [1.0.0] - 2026-11-12

First stable release of the Laravel OPay Checkout integration.

### Added

- Laravel service provider and `Opay` facade with automatic package discovery.
- OPay cashier checkout creation and payment status lookup.
- Direct payment creation and refund requests.
- Webhook parsing and HMAC-SHA3-512 callback signature verification.
- HMAC-SHA512 request signing for authenticated API operations.
- Sandbox and live API endpoint configuration.
- Configurable merchant credentials, request timeout, country, currency, payment method, and cashier expiry.
- Money conversion helper and typed result DTOs.
- Unit tests for payment, cashier, refund, webhook, signature, and money functionality.

### Requirements

- PHP 8.2 or later.
- Laravel components 10, 11, or 12.
- Guzzle 7.
