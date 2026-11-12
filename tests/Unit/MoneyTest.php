<?php

namespace Bhekor\LaravelOpay\Tests\Unit;

use Bhekor\LaravelOpay\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_to_minor_and_major(): void
    {
        $this->assertSame(400000, Money::toMinor(4000));
        $this->assertSame(100, Money::toMinor(1));
        $this->assertSame(4000.0, Money::toMajor(400000));
        $this->assertSame(1.5, Money::toMajor(150));
    }
}
