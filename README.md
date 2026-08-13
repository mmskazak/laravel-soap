Laravel SoapClient Wrapper
===========================

[![Tests](https://github.com/mmskazak/laravel-soap/actions/workflows/tests.yml/badge.svg)](https://github.com/mmskazak/laravel-soap/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/mmskazak/laravel-soap/v/stable)](https://packagist.org/packages/mmskazak/laravel-soap)
[![License](https://poser.pugx.org/mmskazak/laravel-soap/license)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%5E8.0-777bb4)](composer.json)

A SoapClient wrapper integration for Laravel — actively maintained fork of [artisaninweb/laravel-soap](https://github.com/artisaninweb/laravel-soap).

> **Why this fork?** The original package has not been updated since 2021 and targets PHP 5.4+.
> This fork modernizes the codebase for PHP 8.0+, fixes several bugs, and keeps the same simple API.

Please report any bugs or features here: <br/>
https://github.com/mmskazak/laravel-soap/issues/

Requirements
============

- PHP 8.0 or higher
- The `ext-soap` PHP extension
- Laravel or Lumen 9, 10, 11 or 12 (optional — the package also works standalone)

What's changed
==============

- **PHP 8.0+** — dropped support for EOL PHP versions (5.x, 7.x)
- **PSR-4 autoloading** — migrated from deprecated PSR-0
- **Explicit dependency** on `illuminate/support` (^9.0|^10.0|^11.0|^12.0)
- **Fixed** `classMap()` renamed to `classmap()` to match the documented API
- **Fixed** `getOptions()` was mutating internal state on every call — options no longer accumulate on repeated calls
- **Fixed** `client()` closure parameter is now required — previously marked optional but always caused a crash if omitted
- **Improved** `ServiceProvider` uses `singleton()` via the IoC container instead of manual instantiation
- **Cleaner types** — added `string`/`array`/`int` type hints throughout
- **Fixed** `Client::SoapCall()` deprecation on PHP 8.1+ — `array $options = null` changed to `?array $options = null`
- **Fixed** `getOptions()` no longer leaks `null` for `trace`/`cache_wsdl` into the underlying `SoapClient` when those options aren't set explicitly
- **Fixed** `SoapWrapper::call()` now throws a clear `InvalidArgumentException` instead of a PHP warning when the call string isn't in `Service.method` format
- **Added** a publishable configuration file (`config/soapwrapper.php`)
- **Added** a PHPUnit test suite and a GitHub Actions CI workflow (PHP 8.0-8.3)

See [CHANGELOG.md](CHANGELOG.md) for the full release history.

Installation
============

## Laravel

#### Installation (Laravel 9, 10, 11, 12):

Run `composer require mmskazak/laravel-soap`

Laravel 5.5+ supports package auto-discovery, so on **every supported version (9-12)** the
service provider and the `SoapWrapper` facade are registered automatically — no manual step
needed. Manual registration below is only required if you've disabled discovery for this
package (`dont-discover` in your app's `composer.json`).

> **Laravel 11+ note:** the default application skeleton no longer ships a `providers` or
> `aliases` array in `config/app.php` — providers now live in `bootstrap/providers.php`, and
> there's no default facade alias registry. Use the instructions that match your app's skeleton.

**Laravel 9 / 10** (or any app that still has `providers`/`aliases` arrays in `config/app.php`):

```php
<?php
// config/app.php

return [
    // ...

    'providers' => [
        // ...
        Artisaninweb\SoapWrapper\ServiceProvider::class,
    ],

    'aliases' => [
        // ...
        'SoapWrapper' => Artisaninweb\SoapWrapper\Facade::class,
    ],
];
```

**Laravel 11 / 12** (minimal skeleton):

Register the provider in `bootstrap/providers.php`:

```php
<?php

return [
    App\Providers\AppServiceProvider::class,
    Artisaninweb\SoapWrapper\ServiceProvider::class,
];
```

The facade doesn't need an alias — just import it where you use it:

```php
use Artisaninweb\SoapWrapper\Facade as SoapWrapper;
```

If you'd rather keep a global `SoapWrapper` alias like on Laravel 9/10, add an `aliases` array
back to `config/app.php` and register it there the same way.

## Lumen

Open `bootstrap/app.php` and register the required service provider:
```php
$app->register(Artisaninweb\SoapWrapper\ServiceProvider::class);
```

register class alias:
```php
class_alias('Artisaninweb\SoapWrapper\Facade', 'SoapWrapper');
```

*Facades must be enabled.*

## Configuration file (optional)

Services can also be registered through a config file instead of `SoapWrapper::add()`.
Publish it with:

```
php artisan vendor:publish --tag=soapwrapper-config
```

This creates `config/soapwrapper.php`, where each top-level key is a service name mapped to
`Service` setter options (`wsdl`, `trace`, `cache`, `classmap`, `options`, `certificate`):

```php
return [
    'Currency' => [
        'wsdl'  => 'https://www.example.com/service.wsdl',
        'trace' => true,
    ],
];
```

Usage
============

How to add a service to the wrapper and use it.

```php
<?php

namespace App\Http\Controllers;

use Artisaninweb\SoapWrapper\Service;
use Artisaninweb\SoapWrapper\SoapWrapper;
use App\Soap\Request\GetConversionAmount;
use App\Soap\Response\GetConversionAmountResponse;

class SoapController
{
  public function __construct(
    protected SoapWrapper $soapWrapper,
  ) {
  }

  /**
   * Use the SoapWrapper
   */
  public function show(): void
  {
    $this->soapWrapper->add('Currency', function (Service $service): void {
      $service
        ->wsdl('http://currencyconverter.kowabunga.net/converter.asmx?WSDL')
        ->trace(true)
        ->classmap([
          GetConversionAmount::class,
          GetConversionAmountResponse::class,
        ]);
    });

    // Without classmap
    $response = $this->soapWrapper->call('Currency.GetConversionAmount', [
      'CurrencyFrom' => 'USD',
      'CurrencyTo'   => 'EUR',
      'RateDate'     => '2014-06-05',
      'Amount'       => '1000',
    ]);

    var_dump($response);

    // With classmap
    $response = $this->soapWrapper->call('Currency.GetConversionAmount', [
      new GetConversionAmount('USD', 'EUR', '2014-06-05', '1000')
    ]);

    var_dump($response);
    exit;
  }
}
```

Service functions
============
```php
$this->soapWrapper->add('Currency', function (Service $service): void {
    $service
        ->wsdl()                 // The WSDL url
        ->trace(true)            // Optional: (parameter: true/false)
        ->header()               // Optional: (parameters: $namespace,$name,$data,$mustunderstand,$actor)
        ->customHeader()         // Optional: (parameters: $customerHeader) Use this to add a custom SoapHeader or extended class                
        ->cookie()               // Optional: (parameters: $name,$value)
        ->location()             // Optional: (parameter: $location)
        ->certificate()          // Optional: (parameter: $certLocation)
        ->cache(WSDL_CACHE_NONE) // Optional: Set the WSDL cache
    
        // Optional: Set some extra options
        ->options([
            'login' => 'username',
            'password' => 'password'
        ])

        // Optional: Classmap
        ->classmap([
          GetConversionAmount::class,
          GetConversionAmountResponse::class,
        ]);
});
```

Registering services from an array
============

Instead of calling `add()` per service, you can register several at once with `addByArray()` —
this is the same format used by the publishable `config/soapwrapper.php` file:

```php
$this->soapWrapper->addByArray([
    'Currency' => [
        'wsdl'      => 'http://currencyconverter.kowabunga.net/converter.asmx?WSDL',
        'trace'     => true,
        'cache'     => WSDL_CACHE_NONE,
        'classmap'  => [
            GetConversionAmount::class,
            GetConversionAmountResponse::class,
        ],
        'options'   => [
            'login'    => 'username',
            'password' => 'password',
        ],
        'certificate' => storage_path('certs/client.pem'),
    ],
]);
```

Each key must match a `Service` setter method (`wsdl`, `trace`, `cache`, `classmap`, `options`,
`certificate`, `header`, `customHeader`); an unknown key throws `ServiceMethodNotExists`, and a
duplicate service name throws `ServiceAlreadyExists`.

Classmap
============

If you are using classmap you can add folders like for example:
- App\Soap
- App\Soap\Request
- App\Soap\Response

Request: App\Soap\Request\GetConversionAmount

> **Note:** `SoapClient` maps object properties to XML elements by their exact property name, so
> `CurrencyFrom`, `CurrencyTo`, etc. must keep the PascalCase names required by the WSDL — only
> the surrounding syntax is modernized for PHP 8.0+ below.

```php
<?php

namespace App\Soap\Request;

class GetConversionAmount
{
  public function __construct(
    protected string $CurrencyFrom,
    protected string $CurrencyTo,
    protected string $RateDate,
    protected string $Amount,
  ) {
  }

  public function getCurrencyFrom(): string
  {
    return $this->CurrencyFrom;
  }

  public function getCurrencyTo(): string
  {
    return $this->CurrencyTo;
  }

  public function getRateDate(): string
  {
    return $this->RateDate;
  }

  public function getAmount(): string
  {
    return $this->Amount;
  }
}
```

Response: App\Soap\Response\GetConversionAmountResponse

```php
<?php

namespace App\Soap\Response;

class GetConversionAmountResponse
{
  public function __construct(
    protected string $GetConversionAmountResult,
  ) {
  }

  public function getGetConversionAmountResult(): string
  {
    return $this->GetConversionAmountResult;
  }
}
```

Testing
============

```
composer install
composer test
```

The test suite requires the `ext-soap` PHP extension. CI runs it against PHP 8.0 through 8.3
via [`.github/workflows/tests.yml`](.github/workflows/tests.yml).

Contributing
============

Bug reports, feature requests and pull requests are welcome at
https://github.com/mmskazak/laravel-soap/issues/. Please include a failing test case with any
bug report or bug-fix pull request when possible.

License
============

This package is open-sourced software licensed under the [MIT license](LICENSE).
