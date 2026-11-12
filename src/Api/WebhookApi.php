<?php

namespace Bhekor\LaravelOpay\Api;

use Bhekor\LaravelOpay\DTOs\CallbackPayload;
use Bhekor\LaravelOpay\Exceptions\OpayAuthenticationException;
use Bhekor\LaravelOpay\Exceptions\OpayValidationException;
use Bhekor\LaravelOpay\Http\OpayClient;
use Bhekor\LaravelOpay\Support\Signature;

/**
 * @see https://documentation.opaycheckout.com/payment-notifications-callbacks.html
 * @see https://documentation.opaycheckout.com/callback-signature.html
 */
class WebhookApi
{
    public function __construct(protected OpayClient $client) {}

    /**
     * @param  array<string, mixed>  $callbackBody
     */
    public function verify(array $callbackBody): bool
    {
        $secret = $this->client->secretKey();
        if ($secret === '') {
            throw new OpayAuthenticationException('OPay secret key is not configured for webhook verification.');
        }

        return Signature::verifyCallback($callbackBody, $secret);
    }

    /**
     * Verify signature and return a normalized callback payload.
     *
     * @param  array<string, mixed>  $callbackBody
     */
    public function parse(array $callbackBody, bool $verifySignature = true): CallbackPayload
    {
        if (! isset($callbackBody['payload']) || ! is_array($callbackBody['payload'])) {
            throw new OpayValidationException('Callback payload is missing or invalid.');
        }

        if ($verifySignature && ! $this->verify($callbackBody)) {
            throw new OpayAuthenticationException('Invalid OPay callback signature.');
        }

        return CallbackPayload::fromCallbackBody($callbackBody);
    }
}
