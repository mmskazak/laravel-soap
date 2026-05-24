Laravel SoapClient Wrapper
===========================

A SoapClient wrapper integration for Laravel — actively maintained fork of [artisaninweb/laravel-soap](https://github.com/artisaninweb/laravel-soap).

> **Why this fork?** The original package has not been updated since 2021 and targets PHP 5.4+.
> This fork modernizes the codebase for PHP 8.0+, fixes several bugs, and keeps the same simple API.

Please report any bugs or features here: <br/>
https://github.com/mmskazak/laravel-soap/issues/

What's changed
==============

- **PHP 8.0+** — dropped support for EOL PHP versions (5.x, 7.x)
- **PSR-4 autoloading** — migrated from deprecated PSR-0
- **Explicit dependency** on `illuminate/support` (^9.0|^10.0|^11.0)
- **Fixed** `classMap()` renamed to `classmap()` to match the documented API
- **Fixed** `getOptions()` was mutating internal state on every call — options no longer accumulate on repeated calls
- **Fixed** `client()` closure parameter is now required — previously marked optional but always caused a crash if omitted
- **Improved** `ServiceProvider` uses `singleton()` via the IoC container instead of manual instantiation
- **Cleaner types** — added `string`/`array`/`int` type hints throughout

Installation
============

## Laravel

#### Installation (Laravel 9, 10, 11):

Run `composer require mmskazak/laravel-soap`

Laravel 5.5+ supports package auto-discovery, so no manual registration is needed.

If you need to register manually, add the service provider in `config/app.php`:

```php
Artisaninweb\SoapWrapper\ServiceProvider::class,
```

To use the facade, add this to the aliases in `config/app.php`:

```php
'SoapWrapper' => Artisaninweb\SoapWrapper\Facade::class,
```

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


Usage
============

How to add a service to the wrapper and use it.

```php
<?php

namespace App\Http\Controllers;

use Artisaninweb\SoapWrapper\SoapWrapper;
use App\Soap\Request\GetConversionAmount;
use App\Soap\Response\GetConversionAmountResponse;

class SoapController
{
  /**
   * @var SoapWrapper
   */
  protected $soapWrapper;

  /**
   * SoapController constructor.
   *
   * @param SoapWrapper $soapWrapper
   */
  public function __construct(SoapWrapper $soapWrapper)
  {
    $this->soapWrapper = $soapWrapper;
  }

  /**
   * Use the SoapWrapper
   */
  public function show() 
  {
    $this->soapWrapper->add('Currency', function ($service) {
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
$this->soapWrapper->add('Currency', function ($service) {
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

Classmap
============

If you are using classmap you can add folders like for example:
- App\Soap
- App\Soap\Request
- App\Soap\Response

Request: App\Soap\Request\GetConversionAmount

```php
<?php

namespace App\Soap\Request;

class GetConversionAmount
{
  /**
   * @var string
   */
  protected $CurrencyFrom;

  /**
   * @var string
   */
  protected $CurrencyTo;

  /**
   * @var string
   */
  protected $RateDate;

  /**
   * @var string
   */
  protected $Amount;

  /**
   * GetConversionAmount constructor.
   *
   * @param string $CurrencyFrom
   * @param string $CurrencyTo
   * @param string $RateDate
   * @param string $Amount
   */
  public function __construct($CurrencyFrom, $CurrencyTo, $RateDate, $Amount)
  {
    $this->CurrencyFrom = $CurrencyFrom;
    $this->CurrencyTo   = $CurrencyTo;
    $this->RateDate     = $RateDate;
    $this->Amount       = $Amount;
  }

  /**
   * @return string
   */
  public function getCurrencyFrom()
  {
    return $this->CurrencyFrom;
  }

  /**
   * @return string
   */
  public function getCurrencyTo()
  {
    return $this->CurrencyTo;
  }

  /**
   * @return string
   */
  public function getRateDate()
  {
    return $this->RateDate;
  }

  /**
   * @return string
   */
  public function getAmount()
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
  /**
   * @var string
   */
  protected $GetConversionAmountResult;

  /**
   * GetConversionAmountResponse constructor.
   *
   * @param string
   */
  public function __construct($GetConversionAmountResult)
  {
    $this->GetConversionAmountResult = $GetConversionAmountResult;
  }

  /**
   * @return string
   */
  public function getGetConversionAmountResult()
  {
    return $this->GetConversionAmountResult;
  }
}
```
