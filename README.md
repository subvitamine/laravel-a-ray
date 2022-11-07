# Laravel A-ray

[![Latest Version](https://img.shields.io/github/v/tag/subvitamine/laravel-a-ray?sort=semver&label=version)](https://github.com/subvitamine/laravel-a-ray/)
[![Total Downloads](https://img.shields.io/packagist/dt/subvitamine/laravel-a-ray.svg?style=flat-square)](https://packagist.org/packages/subvitamine/laravel-a-ray)

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
use LaravelARay\LaravelARay\ARay;
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

### Handle errors

For handle errors, add this code in your App\Exceptions\Handler.php

```php
use LaravelARay\LaravelARay\ARay;

//...
public function register() {
    $this->reportable(function (Throwable $e) {
        ARay::notifyError($e);
        //...
    });
}
//...

```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
