<?php

namespace GlobalCipta\Common\Routing;

use RuntimeException;
use GlobalCipta\Common\Contracts\Routing\Binder;
use GlobalCipta\Common\Menu\Factory as MenuFactory;

/**
 * Self Binding Route.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
abstract class SelfBindingRoute implements Binder
{
    /**
     * Route path prefix.
     *
     * @var string
     */
    protected $prefix = '/';

    /**
     * Registered route name.
     *
     * @var string
     */
    protected $name;

    /**
     * Router Registrar.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * SelfBindingRoute constructor.
     */
    public function __construct()
    {
        $this->router = app('router');
    }

    /**
     * Bind and register the current route.
     *
     * @return void
     */
    public static function bind()
    {
        $route = new static();

        $route->register();

        $route->afterRegister();
    }

    /**
     * Perform after register callback.
     *
     * @return void
     */
    public function afterRegister()
    {
        if (method_exists($this, 'menu') and app('common.menu') instanceof MenuFactory) {
            $this->menu(app('common.menu'));
        }
    }

    /**
     * Register routes handled by this class.
     *
     * @return void
     */
    abstract public function register();

    /**
     * Use controller method.
     *
     * @param $method string
     * @param $controller string
     *
     * @return string
     */
    public function uses($method, $controller = null)
    {
        if (! method_exists($this, 'controller') and empty($controller)) {
            throw new RuntimeException('Controller is not defined.');
        }

        $controller = empty($controller) ? $this->controller() : $controller;

        return $controller.'@'.$method;
    }

    /**
     * Get route prefix.
     *
     * @param string $path
     *
     * @return string
     */
    public function prefix($path = '/')
    {
        $qualifiedPath = $this->prefix.'/'.ltrim($path, '/');

        return str_replace('//', '/', $qualifiedPath);
    }

    /**
     * Get route name.
     *
     * @param null|string $suffix
     *
     * @return string
     */
    public function name($suffix = null)
    {
        if (empty($suffix)) {
            return $this->name;
        }

        return $this->name.'.'.$suffix;
    }
}
