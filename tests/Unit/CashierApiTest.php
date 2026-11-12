<?php

namespace Bhekor\LaravelOpay\Tests\Unit;

use Bhekor\LaravelOpay\Facades\Opay;
use Bhekor\LaravelOpay\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class CashierApiTest extends TestCase
{
    public function test_cashier_create_returns_normalized_result(): void
    {
        Http::fake([
            'testapi.opaycheckout.com/api/v1/international/cashier/create' => Http::response([
                'code' => '00000',
                'message' => 'SUCCESSFUL',
                'data' => [
                    'reference' => 'ref-1',
                    'orderNo' => 'order-1',
                    'cashierUrl' => 'https://sandboxcashier.opaycheckout.com/checkout?orderToken=TOKEN.abc',
                    'status' => 'INITIAL',
                    'amount' => ['total' => 400, 'currency' => 'NGN'],
                    'vat' => ['total' => 0, 'currency' => 'NGN'],
                ],
            ], 200),
        ]);

        $result = Opay::cashier()->create([
            'country' => 'NG',
            'reference' => 'ref-1',
            'amount' => ['total' => 400, 'currency' => 'NGN'],
            'returnUrl' => 'https://example.com/return',
            'callbackUrl' => 'https://example.com/callback',
            'payMethod' => 'OpayWalletNg',
            'evokeOpay' => true,
            'customerVisitSource' => 'BROWSER',
            'product' => ['name' => 'Wallet top-up', 'description' => 'Utilify'],
        ]);

        $this->assertSame('ref-1', $result->reference);
        $this->assertSame('order-1', $result->orderNo);
        $this->assertStringContainsString('cashier', $result->cashierUrl);
        $this->assertSame('INITIAL', $result->status);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer OPAYPUB_TEST')
                && $request->hasHeader('MerchantId', 'test-merchant-id')
                && ($request['payMethod'] ?? null) === 'OpayWalletNg'
                && ($request['evokeOpay'] ?? null) === true;
        });
    }

    public function test_cashier_status_success(): void
    {
        Http::fake([
            'testapi.opaycheckout.com/api/v1/international/cashier/status' => Http::response([
                'code' => '00000',
                'message' => 'SUCCESSFUL',
                'data' => [
                    'reference' => 'ref-1',
                    'orderNo' => 'order-1',
                    'status' => 'SUCCESS',
                    'amount' => ['total' => 400, 'currency' => 'NGN'],
                ],
            ], 200),
        ]);

        $status = Opay::cashier()->status([
            'country' => 'NG',
            'reference' => 'ref-1',
        ]);

        $this->assertTrue($status->isSuccessful());
        $this->assertSame(4.0, $status->amountMajor());
        $this->assertSame('NGN', $status->currency());
    }

    public function test_cashier_create_api_error_throws(): void
    {
        Http::fake([
            'testapi.opaycheckout.com/api/v1/international/cashier/create' => Http::response([
                'code' => '02004',
                'message' => 'the payment reference(merchant order number) already exists.',
            ], 200),
        ]);

        $this->expectException(\Bhekor\LaravelOpay\Exceptions\OpayValidationException::class);

        Opay::cashier()->create([
            'country' => 'NG',
            'reference' => 'dup',
            'amount' => ['total' => 100, 'currency' => 'NGN'],
            'returnUrl' => 'https://example.com/return',
            'product' => ['name' => 'Test', 'description' => 'Test'],
        ]);
    }
}
