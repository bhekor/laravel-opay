<?php

/**
 * OPay Checkout configuration.
 *
 * Environment is determined by OPAY_BASE_URL (and which keys you use),
 * not a separate OPAY_ENV flag.
 *
 * Sandbox: https://testapi.opaycheckout.com
 * Live:    https://liveapi.opaycheckout.com
 *
 * @see https://documentation.opaycheckout.com/
 */

$baseUrl = rtrim((string) env('OPAY_BASE_URL', 'https://testapi.opaycheckout.com'), '/');

return [

    'base_url' => $baseUrl,

    'merchant_id' => env('OPAY_MERCHANT_ID', ''),

    'public_key' => env('OPAY_PUBLIC_KEY', ''),

    'secret_key' => env('OPAY_SECRET_KEY', ''),

    'timeout' => (int) env('OPAY_TIMEOUT', 60),

    'country' => env('OPAY_COUNTRY', 'NG'),

    'currency' => env('OPAY_CURRENCY', 'NGN'),

    'default_pay_method' => env('OPAY_DEFAULT_PAY_METHOD', 'OpayWalletNg'),

    'evoke_opay' => filter_var(env('OPAY_EVOKE_OPAY', true), FILTER_VALIDATE_BOOLEAN),

    'expire_at_minutes' => (int) env('OPAY_EXPIRE_AT_MINUTES', 30),

];
