<?php

namespace Bhekor\LaravelOpay\Tests;

use Bhekor\LaravelOpay\LaravelOpayServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelOpayServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('opay.merchant_id', 'test-merchant-id');
        $app['config']->set('opay.public_key', 'OPAYPUB_TEST');
        $app['config']->set('opay.secret_key', 'OPAYPRV_TEST_SECRET');
        $app['config']->set('opay.base_url', 'https://testapi.opaycheckout.com');
        $app['config']->set('opay.timeout', 30);
    }
}
