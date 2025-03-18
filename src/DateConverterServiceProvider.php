<?php

namespace Arjanshr\DateConverter;

use Illuminate\Support\ServiceProvider;

class DateConverterServiceProvider extends ServiceProvider
{
    /**
     * Register the services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DateConverter::class, function ($app) {
            return new DateConverter();
        });
    }

    /**
     * Bootstrap the services.
     *
     * @return void
     */
    public function boot()
    {
        // You can load routes, views, or publish configuration files here if needed.
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'mypackage');
    }
}
