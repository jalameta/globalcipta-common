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
            return $factory->json([
                'error' => $error->toArray(),
            ], $error->getHttpStatus());
        });

        $factory->macro('api', function ($data, $httpCode = 200) use ($factory) {
            if ($data instanceof Arrayable) {
                $data = $data->toArray();
            }

            return $factory->json($data, $httpCode);
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
