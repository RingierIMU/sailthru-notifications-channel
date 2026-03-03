<?php

use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Event;
use Mockery;
use NotificationChannels\Sailthru\SailthruChannel;
use NotificationChannels\Sailthru\SailthruClient;
use NotificationChannels\Sailthru\SailthruMessage;
use Sailthru_Client_Exception;

// ---------------------------------------------------------------------------
// Single-recipient send (TEST-06)
// ---------------------------------------------------------------------------

test('single send calls sailthru client send method', function () {
    Event::fake();

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldReceive('send')
        ->once()
        ->with(
            'welcome-template',
            'user@example.com',
            Mockery::type('array'),
            Mockery::type('array'),
        )
        ->andReturn(['status' => 'ok']);

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@example.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('welcome-template'))
                ->toEmail('user@example.com')
                ->replyTo('reply@test.com')
                ->vars(['name' => 'John']);
        }
    };

    $channel->send($notifiable, $notification);
});

test('single send passes correct template and email to client', function () {
    Event::fake();

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldReceive('send')
        ->once()
        ->with(
            'order-confirmation',
            'buyer@shop.com',
            Mockery::type('array'),
            Mockery::type('array'),
        )
        ->andReturn(['status' => 'ok']);

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'buyer@shop.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('order-confirmation'))
                ->toEmail('buyer@shop.com')
                ->replyTo('support@shop.com')
                ->vars(['order_id' => '12345']);
        }
    };

    $channel->send($notifiable, $notification);
});

// ---------------------------------------------------------------------------
// Multi-recipient send (TEST-07)
// ---------------------------------------------------------------------------

test('multi send calls sailthru client multisend method', function () {
    Event::fake();

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldNotReceive('send');
    $client->shouldReceive('multisend')
        ->once()
        ->andReturn(['status' => 'ok']);

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'a@test.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('bulk-template'))
                ->toEmails(['a@test.com', 'b@test.com'])
                ->replyTo('reply@test.com');
        }
    };

    $channel->send($notifiable, $notification);
});

test('multi send passes comma-joined emails and evars', function () {
    Event::fake();

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldNotReceive('send');
    $client->shouldReceive('multisend')
        ->once()
        ->with(
            'bulk-template',
            'a@test.com,b@test.com',
            Mockery::type('array'),
            Mockery::type('array'),
            Mockery::type('array'),
        )
        ->andReturn(['status' => 'ok']);

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'a@test.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('bulk-template'))
                ->toEmails(['a@test.com', 'b@test.com'])
                ->replyTo('reply@test.com')
                ->eVars(['a@test.com' => ['name' => 'A'], 'b@test.com' => ['name' => 'B']]);
        }
    };

    $channel->send($notifiable, $notification);
});

// ---------------------------------------------------------------------------
// Disabled-via-config guard (TEST-09)
// ---------------------------------------------------------------------------

test('send returns empty array when disabled via config', function () {
    config()->set('services.sailthru.enabled', false);

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldNotReceive('send');
    $client->shouldNotReceive('multisend');

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@example.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('any-template'))
                ->toEmail('user@example.com')
                ->replyTo('reply@test.com');
        }
    };

    $result = $channel->send($notifiable, $notification);

    expect($result)->toBe([]);
});

test('send does not call client when disabled', function () {
    config()->set('services.sailthru.enabled', false);

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldNotReceive('send');
    $client->shouldNotReceive('multisend');

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@example.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('any-template'))
                ->toEmail('user@example.com')
                ->replyTo('reply@test.com');
        }
    };

    // Mockery's shouldNotReceive will fail if send or multisend are called
    $channel->send($notifiable, $notification);
});

// ---------------------------------------------------------------------------
// Domain whitelist filtering (TEST-08)
// ---------------------------------------------------------------------------

test('send blocked by domain whitelist returns empty array', function () {
    Event::fake();

    config()->set('services.sailthru.whitelist_check.enabled', true);
    config()->set('services.sailthru.whitelist_check.domains', '*@allowed.com');

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldNotReceive('send');
    $client->shouldNotReceive('multisend');

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@blocked.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('whitelist-template'))
                ->toEmail('user@blocked.com')
                ->replyTo('reply@test.com');
        }
    };

    $result = $channel->send($notifiable, $notification);

    expect($result)->toBe([]);
});

test('send allowed by domain whitelist proceeds normally', function () {
    Event::fake();

    config()->set('services.sailthru.whitelist_check.enabled', true);
    config()->set('services.sailthru.whitelist_check.domains', '*@allowed.com');

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldReceive('send')
        ->once()
        ->andReturn(['status' => 'ok']);

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@allowed.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('whitelist-template'))
                ->toEmail('user@allowed.com')
                ->replyTo('reply@test.com');
        }
    };

    $channel->send($notifiable, $notification);
});

test('whitelist check skipped when not enabled', function () {
    Event::fake();

    config()->set('services.sailthru.whitelist_check.enabled', false);

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldReceive('send')
        ->once()
        ->andReturn(['status' => 'ok']);

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@anydomain.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('any-template'))
                ->toEmail('user@anydomain.com')
                ->replyTo('reply@test.com');
        }
    };

    $channel->send($notifiable, $notification);
});

// ---------------------------------------------------------------------------
// NotificationSent event dispatch (TEST-10)
// ---------------------------------------------------------------------------

test('NotificationSent event dispatched on successful send', function () {
    Event::fake();

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldReceive('send')
        ->once()
        ->andReturn(['status' => 'ok']);

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@example.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('event-template'))
                ->toEmail('user@example.com')
                ->replyTo('reply@test.com');
        }
    };

    $channel->send($notifiable, $notification);

    Event::assertDispatched(NotificationSent::class);
});

test('NotificationSent event contains sailthru channel name', function () {
    Event::fake();

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldReceive('send')
        ->once()
        ->andReturn(['status' => 'ok']);

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@example.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('event-template'))
                ->toEmail('user@example.com')
                ->replyTo('reply@test.com');
        }
    };

    $channel->send($notifiable, $notification);

    Event::assertDispatched(NotificationSent::class, function ($event) {
        return $event->channel === 'sailthru';
    });
});

// ---------------------------------------------------------------------------
// NotificationFailed event dispatch on error (TEST-11)
// ---------------------------------------------------------------------------

test('NotificationFailed event dispatched on Sailthru_Client_Exception', function () {
    Event::fake();

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldReceive('send')
        ->once()
        ->andThrow(new Sailthru_Client_Exception('API error'));

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@example.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('fail-template'))
                ->toEmail('user@example.com')
                ->replyTo('reply@test.com');
        }
    };

    $channel->send($notifiable, $notification);

    Event::assertDispatched(NotificationFailed::class);
});

test('NotificationFailed event contains exception data', function () {
    Event::fake();

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldReceive('send')
        ->once()
        ->andThrow(new Sailthru_Client_Exception('API error'));

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@example.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('fail-template'))
                ->toEmail('user@example.com')
                ->replyTo('reply@test.com');
        }
    };

    $channel->send($notifiable, $notification);

    Event::assertDispatched(NotificationFailed::class, function ($event) {
        return $event->channel === 'sailthru' && isset($event->data['exception']);
    });
});

test('send returns empty array on exception', function () {
    Event::fake();

    $client = Mockery::mock(SailthruClient::class);
    $client->shouldReceive('send')
        ->once()
        ->andThrow(new Sailthru_Client_Exception('API error'));

    $channel = new SailthruChannel($client);

    $notifiable = new class
    {
        public function routeNotificationFor(): string
        {
            return 'user@example.com';
        }
    };

    $notification = new class extends Notification
    {
        public function toSailthru($notifiable): SailthruMessage
        {
            return (new SailthruMessage('fail-template'))
                ->toEmail('user@example.com')
                ->replyTo('reply@test.com');
        }
    };

    $result = $channel->send($notifiable, $notification);

    expect($result)->toBe([]);
});
