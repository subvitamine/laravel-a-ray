<?php

namespace Subvitamine\LaravelARay;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Subvitamine\LaravelARay\Console\AraySendAndPurgeRequests;

class LaravelARayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Kernel $kernel)
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel a-ray');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel a-ray');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');


        $kernel->pushMiddleware(\Subvitamine\LaravelARay\Middleware\BeforeArayMiddleware::class);
        $kernel->pushMiddleware(\Subvitamine\LaravelARay\Middleware\AfterArayMiddleware::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('laravel-a-ray.php'),
            ], 'config');
            // Registering package commands.
            $this->commands([AraySendAndPurgeRequests::class]);


            $this->app->booted(function () {
                $config = config('laravel-a-ray');

                if ($config['api_health']['enabled']) {
                    $schedule = $this->app->make(Schedule::class);
                    $schedule->command('aray:requests-push-purge')->cron($config['api_health']['cron']);
                }
            });
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-a-ray');


    }
}
