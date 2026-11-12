<?php

namespace Bhekor\LaravelOpay\Tests\Unit;

use Bhekor\LaravelOpay\Facades\Opay;
use Bhekor\LaravelOpay\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class PaymentApiTest extends TestCase
{
    public function test_payment_create_qr_response(): void
    {
        Http::fake([
            'testapi.opaycheckout.com/api/v1/international/payment/create' => Http::response([
                'code' => '00000',
                'message' => 'SUCCESSFUL',
                'data' => [
                    'reference' => 'qr-ref',
                    'orderNo' => 'qr-order',
                    'status' => 'PENDING',
                    'nextAction' => [
                        'actionType' => 'SCAN_QR_CODE',
                        'qrCode' => 'https://qrm.operapay.com/example',
                    ],
                    'amount' => ['total' => 60000, 'currency' => 'NGN'],
                ],
            ], 200),
        ]);

        $result = Opay::payments()->create([
            'country' => 'NG',
            'reference' => 'qr-ref',
            'amount' => ['total' => 60000, 'currency' => 'NGN'],
            'payMethod' => 'OpayWalletNgQR',
            'callbackUrl' => 'https://example.com/callback',
            'product' => ['name' => 'Test', 'description' => 'QR'],
        ]);

        $this->assertTrue($result->isQrScan());
        $this->assertSame('https://qrm.operapay.com/example', $result->qrCode());
    }
}
