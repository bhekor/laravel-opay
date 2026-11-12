<?php

namespace Bhekor\LaravelOpay\Http;

use Bhekor\LaravelOpay\Exceptions\OpayApiException;
use Bhekor\LaravelOpay\Exceptions\OpayAuthenticationException;
use Bhekor\LaravelOpay\Exceptions\OpayValidationException;
use Bhekor\LaravelOpay\Support\Signature;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class OpayClient
{
    public function __construct(
        protected string $baseUrl,
        protected string $merchantId,
        protected string $publicKey,
        protected string $secretKey,
        protected int $timeout = 60,
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function merchantId(): string
    {
        return $this->merchantId;
    }

    public function publicKey(): string
    {
        return $this->publicKey;
    }

    public function secretKey(): string
    {
        return $this->secretKey;
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Cashier create uses Bearer {PublicKey}.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function postWithPublicKey(string $path, array $payload): array
    {
        $this->assertCredentials(requireSecret: false);

        return $this->send($path, $payload, 'Bearer '.$this->publicKey);
    }

    /**
     * Status / refund / transaction create use HMAC-SHA512 of the JSON body.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function postWithSignature(string $path, array $payload): array
    {
        $this->assertCredentials(requireSecret: true);

        $json = Signature::encodeJson($payload);
        $signature = Signature::requestHmacSha512($json, $this->secretKey);

        return $this->send($path, $payload, 'Bearer '.$signature, $json);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function send(string $path, array $payload, string $authorization, ?string $rawBody = null): array
    {
        $url = $this->baseUrl.'/'.ltrim($path, '/');
        $body = $rawBody ?? Signature::encodeJson($payload);

        /** @var PendingRequest $request */
        $request = Http::timeout($this->timeout)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => $authorization,
                'MerchantId' => $this->merchantId,
            ])
            ->withBody($body, 'application/json');

        $response = $request->post($url);

        $decoded = $response->json();
        if (! is_array($decoded)) {
            throw new OpayApiException(
                'Invalid JSON response from OPay (HTTP '.$response->status().').',
                null,
                $response->body(),
                $response->status()
            );
        }

        $code = (string) ($decoded['code'] ?? '');
        $message = (string) ($decoded['message'] ?? 'OPay request failed');

        if (! $response->successful()) {
            throw new OpayApiException($message, $code !== '' ? $code : null, $decoded, $response->status());
        }

        // OPay success codes are typically "00000" (also tolerate "00").
        if ($code !== '' && ! in_array($code, ['00000', '00'], true)) {
            if (in_array($code, ['02000'], true)) {
                throw new OpayAuthenticationException($message, $code, $decoded);
            }
            if (in_array($code, ['02001', '02003', '02004', '00001'], true)) {
                throw new OpayValidationException($message, $code, $decoded);
            }

            throw new OpayApiException($message, $code, $decoded);
        }

        return $decoded;
    }

    protected function assertCredentials(bool $requireSecret): void
    {
        if ($this->merchantId === '') {
            throw new OpayAuthenticationException('OPay merchant ID is not configured.');
        }

        if ($this->publicKey === '') {
            throw new OpayAuthenticationException('OPay public key is not configured.');
        }

        if ($requireSecret && $this->secretKey === '') {
            throw new OpayAuthenticationException('OPay secret key is not configured.');
        }
    }
}
