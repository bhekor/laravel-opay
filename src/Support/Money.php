<?php

namespace Bhekor\LaravelOpay\Support;

/**
 * OPay amounts are minor units (100 kobos = 1 NGN).
 *
 * @see https://documentation.opaycheckout.com/regional-payment-methods.html
 */
final class Money
{
    public static function toMinor(float|int|string $major): int
    {
        return (int) round(((float) $major) * 100);
    }

    public static function toMajor(int|float|string $minor): float
    {
        return round(((float) $minor) / 100, 2);
    }
}
