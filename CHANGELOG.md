# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.1.0] - 2026-08-13

### Added
- Support for Laravel 12 (`illuminate/support` `^12.0`)
- Publishable configuration file: `php artisan vendor:publish --tag=soapwrapper-config`
- PHPUnit test suite covering `Service`, `SoapWrapper` and `Client`
- GitHub Actions workflow running the test suite on PHP 8.0-8.3

### Fixed
- `Service::getOptions()` no longer passes `null` for `trace`/`cache_wsdl` to the underlying
  `SoapClient` when those options aren't explicitly set, avoiding a deprecation notice on
  PHP 8.1+ when null is coerced to a non-nullable internal parameter type
- `SoapWrapper::call()` now throws an `InvalidArgumentException` with a clear message instead of
  an "undefined array key" warning when the call string doesn't contain a `Service.method` pair
- Corrected `@var` type annotations on `Service` (`$trace`, `$cache`, `$certificate`)

### Changed
- `Service::$cache` now defaults to `WSDL_CACHE_NONE` and `Service::$trace` defaults to `false`
  instead of being left uninitialized
- README usage examples modernized for PHP 8.0+ (constructor property promotion, typed
  properties/parameters/returns)
- README installation instructions updated for the Laravel 11+ application skeleton
  (`bootstrap/providers.php` instead of `config/app.php`'s `providers`/`aliases` arrays)

## [1.0.1] - 2026-05-25

### Fixed
- `Client::SoapCall()` deprecation on PHP 8.1+ — `array $options = null` changed to
  `?array $options = null`

## [1.0.0] - 2026-05-24

First tagged release of the `mmskazak/laravel-soap` fork.

### Added
- PHP 8.0+ support — dropped support for EOL PHP versions (5.x, 7.x)
- PSR-4 autoloading, migrated from deprecated PSR-0
- Explicit dependency on `illuminate/support` (`^9.0|^10.0|^11.0`)

### Fixed
- `classMap()` renamed to `classmap()` to match the documented API
- `getOptions()` was mutating internal state on every call — options no longer accumulate on
  repeated calls
- `client()` closure parameter is now required — previously marked optional but always caused a
  crash if omitted

### Changed
- `ServiceProvider` uses `singleton()` via the IoC container instead of manual instantiation
- Added `string`/`array`/`int` type hints throughout

See the [README](README.md) for full usage documentation.
