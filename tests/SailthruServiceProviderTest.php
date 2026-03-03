<?php

use Illuminate\Contracts\Support\DeferrableProvider;
use NotificationChannels\Sailthru\SailthruClient;
use NotificationChannels\Sailthru\SailthruServiceProvider;

test('SailthruClient is bound as singleton', function () {
    $instanceA = app()->make(SailthruClient::class);
    $instanceB = app()->make(SailthruClient::class);

    expect($instanceA)->toBe($instanceB);
});

test('SailthruClient is instance of correct class', function () {
    $client = app()->make(SailthruClient::class);

    expect($client)->toBeInstanceOf(SailthruClient::class);
});

test('service provider is deferred', function () {
    $provider = new SailthruServiceProvider($this->app);

    expect($provider)->toBeInstanceOf(DeferrableProvider::class);
});

test('provides returns SailthruClient class', function () {
    $provider = new SailthruServiceProvider($this->app);

    expect($provider->provides())->toBe([SailthruClient::class]);
});
