# Sailthru Notifications Channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/sailthru-notifications-channel.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/sailthru-notifications-channel)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Unit Tests](https://github.com/laravel-notification-channels/sailthru/actions/workflows/main.yml/badge.svg)](https://github.com/laravel-notification-channels/sailthru/actions)

This package provides a [Sailthru](https://www.sailthru.com) notification channel for Laravel, making it easy to send transactional emails via Sailthru templates.

**Requirements:** PHP 8.3+ and Laravel 11 or 12.

## Installation

Install the package via Composer:

```bash
composer require laravel-notification-channels/sailthru-notifications-channel
```

The service provider is auto-discovered -- no manual registration needed.

### Setting up the Sailthru service

Add your Sailthru configuration to `config/services.php`:

```php
'sailthru' => [
    'api_key' => env('SAILTHRU_API_KEY'),
    'secret'  => env('SAILTHRU_SECRET'),

    // Optional: set to false to disable sending (logs instead)
    'enabled' => env('SAILTHRU_ENABLED', true),

    // Optional: restrict sending to specific domains
    'whitelist_check' => [
        'enabled' => env('SAILTHRU_WHITELIST_ENABLED', false),
        'domains' => ['*@yourdomain.com'],
    ],

    // Optional: log API payloads for debugging
    'log_payload' => env('SAILTHRU_LOG_PAYLOAD', false),
],
```

## Usage

### Basic usage -- single recipient

Create a notification with a `toSailthru` method that returns a `SailthruMessage`:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\Sailthru\SailthruChannel;
use NotificationChannels\Sailthru\SailthruMessage;

class OrderShipped extends Notification
{
    public function via(mixed $notifiable): array
    {
        return [SailthruChannel::class];
    }

    public function toSailthru(mixed $notifiable): SailthruMessage
    {
        return SailthruMessage::create('order-shipped')
            ->toEmail($notifiable->email)
            ->toName($notifiable->name)
            ->vars([
                'order_id' => $this->order->id,
                'tracking_url' => $this->order->tracking_url,
            ]);
    }
}
```

### Multi-recipient send

Use `toEmails()` to send to multiple recipients at once, with optional per-recipient variables via `eVars()`:

```php
public function toSailthru(mixed $notifiable): SailthruMessage
{
    return SailthruMessage::create('weekly-digest')
        ->toEmails(['alice@example.com', 'bob@example.com'])
        ->vars(['week' => now()->weekOfYear])
        ->eVars([
            'alice@example.com' => ['first_name' => 'Alice'],
            'bob@example.com'   => ['first_name' => 'Bob'],
        ]);
}
```

### Default variables on the notifiable

Define a `sailthruDefaultVars()` method on your notifiable model to provide variables that are merged into every Sailthru message. Message-specific vars take precedence over default vars.

```php
class User extends Authenticatable
{
    use Notifiable;

    public function sailthruDefaultVars(): array
    {
        return [
            'first_name' => $this->name,
            'account_id' => $this->id,
        ];
    }
}
```

## Available methods

All methods on `SailthruMessage` are fluent (return `$this`):

| Method | Description |
|--------|-------------|
| `create(string $template)` | Static factory -- creates a new message with the given Sailthru template name |
| `template(string $template)` | Override the template name |
| `vars(array $vars)` | Set template variables |
| `eVars(array $eVars)` | Set per-recipient variables for multi-send |
| `to(array $to)` | Set recipient from array (`email`/`address` and `name` keys) |
| `toEmail(string $email)` | Set recipient email address |
| `toName(string $name)` | Set recipient name |
| `toEmails(array $emails)` | Set multiple recipients (enables multi-send) |
| `from(array $from)` | Set sender from array (`email`/`address` and `name` keys) |
| `fromEmail(string $email)` | Set sender email (defaults to `mail.from.address` config) |
| `fromName(string $name)` | Set sender name (defaults to `mail.from.name` config) |
| `replyTo(string $email)` | Set reply-to address |
| `options(array $options)` | Set additional Sailthru API options |

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

```bash
composer test
```

## Security

If you discover any security related issues, please email tools@roam.africa instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- Developed and maintained by [Ringier Tech & Data](http://ringier.tech) and [Ringier One Africa Media](https://roam.africa)
- [Dylan Harbour](https://github.com/dylanharbour)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
