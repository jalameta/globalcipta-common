<?php

namespace GlobalCipta\Common\Testing;

use Illuminate\Routing\Route;
use PHPUnit\Framework\Assert as PHPUnit;

/**
 * Router test.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class TestRouter
{
    /**
     * Route Instance.
     *
     * @var \Illuminate\Routing\Route
     */
    protected $route;

    /**
     * TestRouter constructor.
     *
     * @param \Illuminate\Routing\Route $route
     */
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * Assert uri.
     *
     * @param $uri
     *
     * @return $this
     */
    public function assertUri($uri)
    {
        PHPUnit::assertEquals($uri, $this->route->uri);

        return $this;
    }

    public function assertMiddleware($middleware)
    {
        $middleware = is_array($middleware) ? $middleware : [$middleware];

        foreach ($middleware as $_m) {
            PHPUnit::assertContains($_m, $this->route->action['middleware']);
        }

        return $this;
    }

    public function assertController($controller)
    {
    }

    public function assertActionUses($action)
    {
        PHPUnit::assertEquals($action, $this->route->action['uses']);

        return $this;
    }

    public function assertHttpMethod($method)
    {
        $method = is_array($method) ? $method : [$method];

        foreach ($method as $_m) {
            PHPUnit::assertContains(strtoupper($_m), $this->route->methods);
        }

        return $this;
    }
}
