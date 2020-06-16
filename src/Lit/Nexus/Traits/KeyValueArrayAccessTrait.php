<?php

declare(strict_types=1);

namespace Lit\Nexus\Traits;

/**
 * \ArrayAccess implementation for KeyValueInterface
 */
trait KeyValueArrayAccessTrait
{
    public function offsetExists($offset)
    {
        return $this->exists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    abstract public function set(string $key, $value);

    abstract public function delete(string $key);

    abstract public function get(string $key);

    abstract public function exists(string $key);
}
