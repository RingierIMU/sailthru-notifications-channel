<?php

namespace NotificationChannels\Sailthru\Test;

use NotificationChannels\Sailthru\SailthruServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SailthruServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('services.sailthru', [
            'api_key' => 'test-api-key',
            'secret' => 'test-secret',
            'enabled' => true,
        ]);
    }
}
