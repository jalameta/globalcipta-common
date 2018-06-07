<?php

namespace GlobalCipta\Common\Response;

use Illuminate\Contracts\Support\Arrayable;
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
        $factory->macro('errorApi', function (ErrorApiResponse $error) use ($factory) {
            return $factory->make([
                'error' => $error->toArray()
            ], $error->getHttpStatus(), [
                'Content-Type' => 'application/json'
            ]);
        });

        $factory->macro('api', function(Arrayable $data) use ($factory) {
            return $factory->make($data->toArray(), 200, [
                'Content-Type' => 'application/json'
            ]);
        });
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
