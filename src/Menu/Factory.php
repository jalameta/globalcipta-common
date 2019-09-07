<?php

namespace GlobalCipta\Common\Menu;

use RuntimeException;

/**
 * Menu Factory.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class Factory
{
    /**
     * List of all menu container registered in the application.
     *
     * @var array
     */
    protected $menu = [];

    /**
     * Create new menu container.
     *
     * @param $key
     * @param array $options
     * @param array $items
     *
     * @return Container
     */
    public function make($key, array $options = [], array $items = [])
    {
        if (array_key_exists($key, $this->menu) === false) {
            $this->menu[$key] = new Container($items, $options);
        } else {
            throw new RuntimeException("Menu with key: `$key` already exist.");
        }

        return $this->get($key);
    }

    /**
     * Get menu container by its key.
     *
     * @param $key
     *
     * @return Container
     */
    public function get($key): Container
    {
        if (array_key_exists($key, $this->menu)) {
            return $this->menu[$key];
        }

        throw new RuntimeException("Menu with key: `$key` doesn't exists.");
    }
}
