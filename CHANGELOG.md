# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.0.0] - 2026-08-13

First tagged release of the `mmskazak/laravel-soap` fork.

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

See the [README](README.md) for the full list of changes carried over from the initial
modernization of this fork (PHP 8.0+ support, PSR-4 autoloading, etc).
