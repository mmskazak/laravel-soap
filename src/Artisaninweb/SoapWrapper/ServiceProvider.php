<?php

namespace Artisaninweb\SoapWrapper;

use \Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
  /**
   * Bootstrap the application events.
   *
   * @return void
   */
  public function boot()
  {
    // Nothing here
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->app->singleton(SoapWrapper::class, function ($app) {
      $soapWrapper = new SoapWrapper();

      $config = $app['config']['soapwrapper'] ?? null;

      if (is_array($config)) {
        $soapWrapper->addByArray($config);
      }

      return $soapWrapper;
    });
  }
}
