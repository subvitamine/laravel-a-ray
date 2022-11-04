# Laravel A-ray

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-a-ray/laravel a-ray.svg?style=flat-square)](https://packagist.org/packages/laravel-a-ray/laravel a-ray)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-a-ray/laravel a-ray.svg?style=flat-square)](https://packagist.org/packages/laravel-a-ray/laravel a-ray)

This package assist create push on a-ray from laravel.

## Installation

You can install the package via composer:

```bash
composer require subvitamine/laravel-a-ray
```

## Usage

Add on your .env file

```dotenv
A_RAY_ENABLED=true#default true
A_RAY_PRIVATE_KEY=pk_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
A_RAY_NOTIFY_ERRORS_ENABLED=true#default false
A_RAY_NOTIFY_SLACK_WEBHOOK=https://hooks.slack.com/services/XXXXXXXXX/XXXXXXXXX/XXXXXXXXXXXXXXXXXXXXXXXX
```

Use on your code

```php
use Subvitamine\LaravelARay\ARay;
use LaravelARay\LaravelARay\CommitStatus;

// Check config
ARay::checkConfig()

// Init push
$push = ARay::initPush()

/**
* Add a commit status
 * All status : 
 * SUCCESS
 * INFO
 * WARNING
 * ERROR
 */
$push->addCommit('commit message', ['commit' => 'data'], CommitStatus::SUCCESS)

// Send push
ARay::sendPush($push)
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
