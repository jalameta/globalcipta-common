<?php

namespace GlobalCipta\Common\Menu;

use Closure;
use ArrayAccess;
use JsonSerializable;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Menu Item.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class Item implements Jsonable, Arrayable, JsonSerializable, ArrayAccess
{
    /**
     * Item children.
     *
     * @var mixed
     */
    public $children;
    /**
     * Item options.
     *
     * @var array
     */
    protected $options;

    /**
     * Render if callback.
     *
     * @var Closure
     */
    protected $renderableResolver;

    /**
     * Active state resolver.
     *
     * @var Closure
     */
    protected $activeStateResolver;

    /**
     * Badge counter resolver.
     *
     * @var Closure
     */
    protected $badgeContentResolver;

    /**
     * Item constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $default = [
            'route' => '',
            'css_class' => [],
            'order' => 100,
            'text' => '',
            'badge' => false,
            'uri' => '',
            'icon' => '',
            'active_class' => 'active',
            'children' => null,
            'is_separator' => false,
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
        if ($this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        throw new InvalidArgumentException('Invalid attribute `'.$key.'`');
    }

    /**
     * Check if this item has badges.
     *
     * @return bool
     */
    public function hasBadge(): bool
    {
        return filter_var($this->badge, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Determine current active resolver.
     *
     * @return bool
     */
    public function active(): bool
    {
        if ($this->activeStateResolver instanceof Closure) {
            $result = call_user_func($this->activeStateResolver);

            return filter_var($result, FILTER_VALIDATE_BOOLEAN);
        }

        return Str::startsWith(request()->route()->getName(), $this->route);
    }

    /**
     * Get Badge Content.
     *
     * @return mixed
     */
    public function getBadgeContent()
    {
        if ($this->badgeContentResolver instanceof Closure) {
            return call_user_func($this->badgeContentResolver);
        }

        return '';
    }

    /**
     * CSS classes for this item.
     *
     * @return string
     */
    public function cssClasses(): string
    {
        return implode(' ', $this->css_class);
    }

    /**
     * Determine if this item is menu separator.
     *
     * @return bool
     */
    public function isSeparator(): bool
    {
        return $this->options['is_separator'];
    }

    /**
     * Determine if this item should be rendered.
     *
     * @return bool
     */
    public function renderable(): bool
    {
        // determine if this item is available for rendering
        if ($this->renderableResolver instanceof Closure) {
            $result = call_user_func($this->renderableResolver);

            return filter_var($result, FILTER_VALIDATE_BOOLEAN);
        }

        return true;
    }

    /**
     * Renderable resolver.
     *
     * @param Closure $callback
     * @return $this
     */
    public function renderableResolver(Closure $callback): self
    {
        $this->renderableResolver = $callback;

        return $this;
    }

    /**
     * Active state resolver callback.
     *
     * @param Closure $callback
     * @return $this
     */
    public function activeStateResolver(Closure $callback): self
    {
        $this->activeStateResolver = $callback;

        return $this;
    }

    /**
     * Badge content resolver.
     *
     * @param Closure $callback
     * @return $this
     */
    public function badgeContentResolver(Closure $callback): self
    {
        $this->badgeContentResolver = $callback;

        return $this;
    }

    /**
     * Create new menu item.
     *
     * @param string $uri
     * @param string $text
     * @param array $options
     */
    public function addChild($uri, $text, $options = [])
    {
        if ($this->children instanceof Container === false) {
            $this->children = new Container();
        }

        /*
         * @var \GlobalCipta\Common\Menu\Container $this->children
         */
        $this->children->add($uri, $text, $options);
    }

    /**
     * Check if this item has children.
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return ($this->children instanceof Container and $this->children->count() > 0);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            } elseif ($value instanceof Jsonable) {
                return json_decode($value->toJson(), true);
            } elseif ($value instanceof Arrayable) {
                return $value->toArray();
            } else {
                return $value;
            }
        }, $this->options);
    }

    /**
     * Get the collection of items as JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->options;
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->options);
    }

    /**
     * Offset to retrieve.
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->options[$offset];
    }

    /**
     * Offset to set.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->options[$offset] = $value;
    }

    /**
     * Offset to unset.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->options[$offset]);
    }
}
