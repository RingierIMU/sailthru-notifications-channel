<?php

namespace NotificationChannels\Sailthru;

use Illuminate\Support\ServiceProvider;
use Sailthru_Client;

class SailthruServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this
            ->app
            ->when(SailthruChannel::class)
            ->needs(Sailthru_Client::class)
            ->give(function () {
                $config = $this->app['config'];

                return new Sailthru_Client(
                    $config->get('services.sailthru.api_key'),
                    $config->get('services.sailthru.secret')
                );
            });
    }
}
