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
        $this->publishes([
            __DIR__ . '/../config/bs_date.php' => config_path('bs_date.php'),
        ], 'config');
    }
}
