<?php

namespace Bhekor\LaravelOpay\Tests\Unit;

use Bhekor\LaravelOpay\Support\Signature;
use PHPUnit\Framework\TestCase;

class SignatureTest extends TestCase
{
    public function test_request_hmac_sha512_is_stable(): void
    {
        $body = '{"country":"NG","reference":"abc"}';
        $secret = 'OPAYPRV_TEST_SECRET';

        $sig1 = Signature::requestHmacSha512($body, $secret);
        $sig2 = Signature::requestHmacSha512($body, $secret);

        $this->assertSame($sig1, $sig2);
        $this->assertSame(128, strlen($sig1));
    }

    public function test_callback_signature_round_trip(): void
    {
        $secret = 'OPAYPRV_TEST_SECRET';
        $payload = [
            'amount' => '49160',
            'currency' => 'NGN',
            'reference' => '10023',
            'refunded' => false,
            'status' => 'SUCCESS',
            'timestamp' => '2022-05-07T06:20:46Z',
            'token' => '220507145660712931829',
            'transactionId' => '220507145660712931829',
        ];

        $sha512 = Signature::callbackHmacSha3_512($payload, $secret);

        $this->assertTrue(Signature::verifyCallback([
            'payload' => $payload,
            'sha512' => $sha512,
            'type' => 'transaction-status',
        ], $secret));

        $this->assertFalse(Signature::verifyCallback([
            'payload' => $payload,
            'sha512' => 'deadbeef',
            'type' => 'transaction-status',
        ], $secret));
    }

    public function test_encode_json_does_not_escape_slashes(): void
    {
        $json = Signature::encodeJson(['url' => 'https://example.com/path']);
        $this->assertStringContainsString('https://example.com/path', $json);
        $this->assertStringNotContainsString('\\/', $json);
    }
}
