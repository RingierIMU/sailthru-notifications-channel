<?php

namespace NotificationChannels\Sailthru;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SailthruServiceProvider extends ServiceProvider implements DeferrableProvider
{
    #[\Override]
    public function register(): void
    {
        $this->app->singleton(
            SailthruClient::class,
            fn(Application $app) => new SailthruClient(
                $app['config']->get('services.sailthru.api_key'),
                $app['config']->get('services.sailthru.secret')
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function provides(): array
    {
        return [SailthruClient::class];
    }
}
