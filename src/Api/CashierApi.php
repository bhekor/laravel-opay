<?php

namespace Bhekor\LaravelOpay\Api;

use Bhekor\LaravelOpay\DTOs\CashierCreateResult;
use Bhekor\LaravelOpay\DTOs\PaymentStatusResult;
use Bhekor\LaravelOpay\Exceptions\OpayApiException;
use Bhekor\LaravelOpay\Exceptions\OpayValidationException;
use Bhekor\LaravelOpay\Http\OpayClient;

class CashierApi
{
    public function __construct(protected OpayClient $client) {}

    /**
     * Create an OPay Cashier payment.
     *
     * Preferred Utilify flow: payMethod=OpayWalletNg, evokeOpay=true.
     *
     * @param  array<string, mixed>  $payload
     *
     * @see https://documentation.opaycheckout.com/cashier-create.html
     */
    public function create(array $payload): CashierCreateResult
    {
        $this->assertRequired($payload, ['reference', 'country', 'amount', 'returnUrl', 'product']);

        if (! is_array($payload['amount'] ?? null)
            || ! isset($payload['amount']['total'], $payload['amount']['currency'])) {
            throw new OpayValidationException('amount.total and amount.currency are required.');
        }

        if (! is_array($payload['product'] ?? null) || empty($payload['product']['name'])) {
            throw new OpayValidationException('product.name is required.');
        }

        $response = $this->client->postWithPublicKey('/api/v1/international/cashier/create', $payload);
        $data = $response['data'] ?? null;

        if (! is_array($data) || empty($data['cashierUrl'])) {
            throw new OpayApiException(
                'OPay cashier create did not return a cashierUrl.',
                isset($response['code']) ? (string) $response['code'] : null,
                $response
            );
        }

        return CashierCreateResult::fromApiData($data, $response);
    }

    /**
     * Query cashier / payment status by merchant reference and/or OPay orderNo.
     *
     * @param  array{reference?: string, orderNo?: string, country: string}  $payload
     *
     * @see https://documentation.opaycheckout.com/query-payment-status.html
     */
    public function status(array $payload): PaymentStatusResult
    {
        if (empty($payload['country'])) {
            throw new OpayValidationException('country is required.');
        }

        if (empty($payload['reference']) && empty($payload['orderNo'])) {
            throw new OpayValidationException('reference or orderNo is required.');
        }

        $response = $this->client->postWithSignature('/api/v1/international/cashier/status', $payload);
        $data = $response['data'] ?? null;

        if (! is_array($data)) {
            throw new OpayApiException(
                'OPay status query returned no data.',
                isset($response['code']) ? (string) $response['code'] : null,
                $response
            );
        }

        return PaymentStatusResult::fromApiData($data, $response);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  list<string>  $keys
     */
    protected function assertRequired(array $payload, array $keys): void
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $payload) || $payload[$key] === null || $payload[$key] === '') {
                throw new OpayValidationException("Missing required field: {$key}");
            }
        }
    }
}
