<?php

namespace GlobalCipta\Common\Routing;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Route Group Service Provider.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class RouteServiceProvider extends LaravelServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('common.routing', function () {
            $factory = new Factory();

            return $factory;
        });

        $this->app->alias('common.routing', Factory::class);
    }
}
