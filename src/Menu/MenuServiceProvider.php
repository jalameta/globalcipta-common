<?php

namespace GlobalCipta\Common\Menu;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Menu Service Provider.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class MenuServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'common-menu');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('common.menu', function () {
            $factory = new Factory();

            return $factory;
        });

        $this->app->alias('common.menu', Factory::class);
    }
}
