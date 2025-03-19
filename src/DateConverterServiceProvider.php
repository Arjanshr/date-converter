<?php
namespace Arjanshr\DateConverter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

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
        // Publish the configuration file to the application's config directory
        $this->publishes([
            __DIR__ . '/config/bs_date.php' => config_path('bs_date.php'),
        ], 'config');
    }
}
