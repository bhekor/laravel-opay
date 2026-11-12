<?php

namespace Bhekor\LaravelOpay\Support;

/**
 * OPay request and callback signature helpers.
 *
 * Outbound APIs: HMAC-SHA512 of the JSON body with the secret key.
 * Cashier create: Bearer {PublicKey} (no HMAC).
 * Callbacks: HMAC-SHA3-512 over a fixed field string.
 *
 * @see https://documentation.opaycheckout.com/api-signature.html
 * @see https://documentation.opaycheckout.com/callback-signature.html
 */
final class Signature
{
    /**
     * HMAC-SHA512 hex digest of a JSON request body string.
     */
    public static function requestHmacSha512(string $jsonBody, string $secretKey): string
    {
        return hash_hmac('sha512', $jsonBody, $secretKey);
    }

    /**
     * Build the canonical callback signing string, then HMAC-SHA3-512.
     *
     * Format from OPay docs:
     * {Amount:"…",Currency:"…",Reference:"…",Refunded:t|f,Status:"…",Timestamp:"…",Token:"…",TransactionID:"…"}
     *
     * @param  array<string, mixed>  $payload  The `payload` object from the callback body
     */
    public static function callbackHmacSha3_512(array $payload, string $secretKey): string
    {
        $amount = (string) ($payload['amount'] ?? '');
        $currency = (string) ($payload['currency'] ?? '');
        $reference = (string) ($payload['reference'] ?? '');
        $refunded = ! empty($payload['refunded']) ? 't' : 'f';
        $status = (string) ($payload['status'] ?? '');
        $timestamp = (string) ($payload['timestamp'] ?? '');
        $token = (string) ($payload['token'] ?? '');
        $transactionId = (string) ($payload['transactionId'] ?? '');

        $signContent = sprintf(
            '{Amount:"%s",Currency:"%s",Reference:"%s",Refunded:%s,Status:"%s",Timestamp:"%s",Token:"%s",TransactionID:"%s"}',
            $amount,
            $currency,
            $reference,
            $refunded,
            $status,
            $timestamp,
            $token,
            $transactionId
        );

        return hash_hmac('sha3-512', $signContent, $secretKey);
    }

    /**
     * @param  array<string, mixed>  $callbackBody  Full callback JSON (`payload` + `sha512`)
     */
    public static function verifyCallback(array $callbackBody, string $secretKey): bool
    {
        $provided = (string) ($callbackBody['sha512'] ?? '');
        if ($provided === '' || $secretKey === '') {
            return false;
        }

        $payload = $callbackBody['payload'] ?? null;
        if (! is_array($payload)) {
            return false;
        }

        $computed = self::callbackHmacSha3_512($payload, $secretKey);

        return hash_equals(strtolower($computed), strtolower($provided));
    }

    /**
     * Encode payload for signing / POST. Uses unescaped slashes to match OPay samples.
     *
     * @param  array<string, mixed>  $data
     */
    public static function encodeJson(array $data): string
    {
        return (string) json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
