<?php

namespace GlobalCipta\Common\Testing;

/**
 * Interact with routes collection.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
trait WithRoutes
{
    /**
     * Get Routes Collection.
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    public function routes()
    {
        return app('router')->getRoutes();
    }

    /**
     * Get route by name.
     *
     * @param $name
     *
     * @return \GlobalCipta\Common\Testing\TestRouter
     */
    public function getRouteByName($name)
    {
        $route = $this->routes()->getByName($name);

        return is_null($route) ? null : new TestRouter($route);
    }
}
