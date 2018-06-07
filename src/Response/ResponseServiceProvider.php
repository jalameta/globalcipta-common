<?php

namespace GlobalCipta\Common\Response;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Response service provider
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class ResponseServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @param \Illuminate\Routing\ResponseFactory $factory
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // nothing to see here, get lost.
    }
}
