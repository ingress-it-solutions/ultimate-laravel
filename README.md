# Real-Time monitoring package using Palzin Monitor

[![Latest Stable Version](http://poser.pugx.org/ultimate-apm/ultimate-laravel/v)](https://packagist.org/packages/ultimate-apm/ultimate-laravel) [![Total Downloads](http://poser.pugx.org/ultimate-apm/ultimate-laravel/downloads)](https://packagist.org/packages/ultimate-apm/ultimate-laravel) [![License](http://poser.pugx.org/ultimate-apm/ultimate-laravel/license)](https://packagist.org/packages/ultimate-apm/ultimate-laravel)

Simple code execution monitoring and bug reporting for Laravel developers.


- [Requirements](#requirements)
- [Installation](#installation)
- [Configure the Ingestion Key](#key)
- [Middleware Setup](#middleware)
- [Test everything is working](#test)

<a name="requirements"></a>

## Requirements

- PHP >= 7.2.0
- Laravel >= 5.5

<a name="install"></a>

## Install



Install the latest version of our package by:

```
composer require ultimate-apm/ultimate-laravel
```

## For Lumen
If your application is based on Lumen you need to manually register the `UltimateServiceProvider`:

```php
$app->register(\Ultimate\Laravel\UltimateServiceProvider::class);
```


<a name="key"></a>

### Configure the Ingestion Key

First put the Ingestion Key in your environment file:

```
ULTIMATE_INGESTION_KEY=[your ingestion key]
```

You can obtain an `ULTIMATE_INGESTION_KEY` creating a new project in your [Palzin APM](https://www.palzin.app) account.

<a name="middleware"></a>

### Attach the Middleware

To monitor web requests you can attach the `WebMonitoringMiddleware` in your http kernel or use in one or more route groups based on your personal needs.

```php
/**
 * The application's route middleware groups.
 *
 * @var array
 */
protected $middlewareGroups = [
    'web' => [
        ...,
        \Ultimate\Laravel\Middleware\WebRequestMonitoring::class,
    ],

    'api' => [
        ...,
        \Ultimate\Laravel\Middleware\WebRequestMonitoring::class,
    ]
```

<a name="test"></a>

### Test everything is working

Run the command below:

```
php artisan ultimate:test
```

Go to [https://www.palzin.app/](https://www.palzin.app/) to explore your data.

## LICENSE

This package is licensed under the [MIT](LICENSE) license.
