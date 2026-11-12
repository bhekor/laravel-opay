<?php

namespace Bhekor\LaravelOpay\Tests\Unit;

use Bhekor\LaravelOpay\Exceptions\OpayAuthenticationException;
use Bhekor\LaravelOpay\Facades\Opay;
use Bhekor\LaravelOpay\Support\Signature;
use Bhekor\LaravelOpay\Tests\TestCase;

class WebhookApiTest extends TestCase
{
    public function test_parse_valid_callback(): void
    {
        $payload = [
            'amount' => '400',
            'currency' => 'NGN',
            'reference' => 'ref-1',
            'refunded' => false,
            'status' => 'SUCCESS',
            'timestamp' => '2022-05-07T06:20:46Z',
            'token' => 'tok-1',
            'transactionId' => 'txn-1',
            'instrumentType' => 'OpayWalletNg',
        ];

        $body = [
            'payload' => $payload,
            'sha512' => Signature::callbackHmacSha3_512($payload, 'OPAYPRV_TEST_SECRET'),
            'type' => 'transaction-status',
        ];

        $parsed = Opay::webhooks()->parse($body);

        $this->assertTrue($parsed->isSuccessful());
        $this->assertSame('ref-1', $parsed->reference);
        $this->assertSame('txn-1', $parsed->transactionId);
    }

    public function test_parse_rejects_invalid_signature(): void
    {
        $this->expectException(OpayAuthenticationException::class);

        Opay::webhooks()->parse([
            'payload' => [
                'amount' => '400',
                'currency' => 'NGN',
                'reference' => 'ref-1',
                'refunded' => false,
                'status' => 'SUCCESS',
                'timestamp' => '2022-05-07T06:20:46Z',
                'token' => 'tok-1',
                'transactionId' => 'txn-1',
            ],
            'sha512' => 'invalid',
            'type' => 'transaction-status',
        ]);
    }
}
