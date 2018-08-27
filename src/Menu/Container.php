<?php

namespace GlobalCipta\Common\Menu;

use InvalidArgumentException;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

/**
 * Menu Container.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class Container extends Collection
{
    /**
     * Options Array.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Create a new menu container.
     *
     * @param mixed  $items,
     * @param array $options
     */
    public function __construct($items = [], array $options = [])
    {
        parent::__construct($items);

        $default = [
            'css_class' => [],
            'view' => 'core-menu::menu',
        ];

        $this->options = array_merge($default, $options);
    }

    /**
     * Object getter.
     *
     * @param $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->options)) {
            return $this->options[$key];
        }

        throw new InvalidArgumentException('Invalid attribute `'.$key.'`');
    }

    /**
     * Sort Items.
     *
     * @return $this
     */
    public function sortItems()
    {
        $sorted = $this->sortBy('order');

        $this->items = $sorted;

        return $this;
    }

    /**
     * Create new menu item.
     *
     * @param string $uri
     * @param string $text
     * @param array $options
     *
     * @return \GlobalCipta\Common\Menu\Item
     */
    public function add($uri, $text, $options = [])
    {
        $item = new Item(array_merge(['uri' => $uri, 'text' => $text], $options));

        $this->offsetSet($uri, $item);

        return $item;
    }

    /**
     * Create new menu item separator.
     *
     * @param int $order
     * @param string $text
     *
     * @return \GlobalCipta\Common\Menu\Item
     */
    public function addSeparator($order, $text = null)
    {
        $item = new Item([
            'is_separator' => true,
            'order' => $order,
            'text' => $text,
        ]);

        $this->offsetSet('separator-'.$order, $item);

        return $item;
    }

    /**
     * css classes representation.
     *
     * @return string
     */
    public function cssClasses()
    {
        return implode(' ', $this->css_class);
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @param string $view
     * @return string
     */
    public function render($view = null)
    {
        return new HtmlString(view()->make($view ?: $this->view, [
            'menu' => $this,
        ])->render());
    }
}
