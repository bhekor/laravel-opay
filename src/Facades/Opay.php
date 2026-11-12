<?php

namespace Bhekor\LaravelOpay\Facades;

use Bhekor\LaravelOpay\Api\CashierApi;
use Bhekor\LaravelOpay\Api\PaymentApi;
use Bhekor\LaravelOpay\Api\RefundApi;
use Bhekor\LaravelOpay\Api\WebhookApi;
use Bhekor\LaravelOpay\Http\OpayClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method static OpayClient client()
 * @method static CashierApi cashier()
 * @method static PaymentApi payments()
 * @method static RefundApi refunds()
 * @method static WebhookApi webhooks()
 *
 * @see \Bhekor\LaravelOpay\Opay
 */
class Opay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'opay';
    }
}
