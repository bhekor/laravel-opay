<?php

namespace Bhekor\LaravelOpay;

use Bhekor\LaravelOpay\Api\CashierApi;
use Bhekor\LaravelOpay\Api\PaymentApi;
use Bhekor\LaravelOpay\Api\RefundApi;
use Bhekor\LaravelOpay\Api\WebhookApi;
use Bhekor\LaravelOpay\Http\OpayClient;

class Opay
{
    protected ?CashierApi $cashier = null;

    protected ?PaymentApi $payments = null;

    protected ?RefundApi $refunds = null;

    protected ?WebhookApi $webhooks = null;

    public function __construct(protected OpayClient $client) {}

    public function client(): OpayClient
    {
        return $this->client;
    }

    public function cashier(): CashierApi
    {
        return $this->cashier ??= new CashierApi($this->client);
    }

    public function payments(): PaymentApi
    {
        return $this->payments ??= new PaymentApi($this->client);
    }

    public function refunds(): RefundApi
    {
        return $this->refunds ??= new RefundApi($this->client);
    }

    public function webhooks(): WebhookApi
    {
        return $this->webhooks ??= new WebhookApi($this->client);
    }
}
