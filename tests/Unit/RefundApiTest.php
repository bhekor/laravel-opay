<?php

namespace Bhekor\LaravelOpay\Tests\Unit;

use Bhekor\LaravelOpay\Facades\Opay;
use Bhekor\LaravelOpay\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class RefundApiTest extends TestCase
{
    public function test_refund_create(): void
    {
        Http::fake([
            'testapi.opaycheckout.com/api/v1/international/payment/refund/create' => Http::response([
                'code' => '00000',
                'message' => 'SUCCESSFUL',
                'data' => [
                    'reference' => 'refund-1',
                    'originalReference' => 'ref-1',
                    'orderNo' => 'r-order',
                    'originalOrderNo' => 'order-1',
                    'country' => 'NG',
                    'refundAmount' => ['total' => 400, 'currency' => 'NGN'],
                    'orderStatus' => 'SUCCESS',
                ],
            ], 200),
        ]);

        $result = Opay::refunds()->create([
            'reference' => 'refund-1',
            'originalReference' => 'ref-1',
            'country' => 'NG',
            'refundWay' => 'Original',
            'amount' => ['total' => 400, 'currency' => 'NGN'],
            'callbackUrl' => 'https://example.com/callback',
        ]);

        $this->assertSame('SUCCESS', $result->orderStatus);
        $this->assertSame('ref-1', $result->originalReference);
    }
}
