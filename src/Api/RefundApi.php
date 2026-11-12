<?php

namespace Bhekor\LaravelOpay\Api;

use Bhekor\LaravelOpay\DTOs\RefundResult;
use Bhekor\LaravelOpay\Exceptions\OpayApiException;
use Bhekor\LaravelOpay\Exceptions\OpayValidationException;
use Bhekor\LaravelOpay\Http\OpayClient;

/**
 * @see https://documentation.opaycheckout.com/payment-refund.html
 */
class RefundApi
{
    public function __construct(protected OpayClient $client) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): RefundResult
    {
        foreach (['reference', 'country', 'originalReference', 'refundWay', 'amount', 'callbackUrl'] as $key) {
            if (! array_key_exists($key, $payload) || $payload[$key] === null || $payload[$key] === '') {
                throw new OpayValidationException("Missing required field: {$key}");
            }
        }

        if (! is_array($payload['amount'] ?? null)
            || ! isset($payload['amount']['total'], $payload['amount']['currency'])) {
            throw new OpayValidationException('amount.total and amount.currency are required.');
        }

        $response = $this->client->postWithSignature('/api/v1/international/payment/refund/create', $payload);
        $data = $response['data'] ?? null;

        if (! is_array($data)) {
            throw new OpayApiException(
                'OPay refund create returned no data.',
                isset($response['code']) ? (string) $response['code'] : null,
                $response
            );
        }

        return RefundResult::fromApiData($data, $response);
    }
}
