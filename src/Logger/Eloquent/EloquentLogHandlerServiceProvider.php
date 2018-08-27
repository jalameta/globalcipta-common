<?php

namespace GlobalCipta\Common\Logger\Eloquent;

use Illuminate\Support\ServiceProvider;

/**
 * Eloquent Log Handler Service Provider.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class EloquentLogHandlerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // make sure this process only run in console
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../../../database/migrations/logger');
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
