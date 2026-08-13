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
    $this->publishes([
      __DIR__ . '/../../../config/soapwrapper.php' => config_path('soapwrapper.php'),
    ], 'soapwrapper-config');
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
    $this->mergeConfigFrom(__DIR__ . '/../../../config/soapwrapper.php', 'soapwrapper');

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
