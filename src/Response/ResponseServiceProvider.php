<?php

namespace GlobalCipta\Common\Response;

use Illuminate\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Response service provider.
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
                'error' => $error->toArray(),
            ], $error->getHttpStatus(), [
                'Content-Type' => 'application/json',
            ]);
        });

        $factory->macro('api', function ($data) use ($factory) {
            if ($data instanceof Arrayable) {
                $data = $data->toArray();
            }

            return $factory->make($data, 200, [
                'Content-Type' => 'application/json',
            ]);
        });

        require_once __DIR__.'/helpers.php';
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
