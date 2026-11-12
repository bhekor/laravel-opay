<?php

namespace Bhekor\LaravelOpay;

use Bhekor\LaravelOpay\Http\OpayClient;
use Illuminate\Support\ServiceProvider;

class LaravelOpayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/opay.php', 'opay');

        $this->app->singleton(OpayClient::class, function ($app) {
            return new OpayClient(
                baseUrl: (string) config('opay.base_url'),
                merchantId: (string) config('opay.merchant_id'),
                publicKey: (string) config('opay.public_key'),
                secretKey: (string) config('opay.secret_key'),
                timeout: (int) config('opay.timeout', 60),
            );
        });

        $this->app->singleton(Opay::class, function ($app) {
            return new Opay($app->make(OpayClient::class));
        });

        $this->app->alias(Opay::class, 'opay');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/opay.php' => config_path('opay.php'),
            ], 'opay-config');
        }
    }
}
