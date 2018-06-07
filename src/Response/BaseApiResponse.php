<?php

namespace GlobalCipta\Common\Response;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Base api response
 *
 * @author      veelasky <veelasky@gmail.com>
 */
abstract class BaseApiResponse implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    public function toJson($options = 0)
    {
        // TODO: Implement toJson() method.
    }

    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
    }

    public function toArray()
    {
        // TODO: Implement toArray() method.
    }

    public function offsetExists($offset)
    {
        // TODO: Implement offsetExists() method.
    }

    public function offsetGet($offset)
    {
        // TODO: Implement offsetGet() method.
    }

    public function offsetSet($offset, $value)
    {
        // TODO: Implement offsetSet() method.
    }

    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }
}
