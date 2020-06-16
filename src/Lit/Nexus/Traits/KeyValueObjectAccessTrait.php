<?php

declare(strict_types=1);

namespace Lit\Nexus\Traits;

/**
 * Magic methods for KeyValueInterface, makes it's content accessible as object properties.
 */
trait KeyValueObjectAccessTrait
{
    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __isset($name)
    {
        return $this->exists($name);
    }

    public function __unset($name)
    {
        $this->delete($name);
    }

    abstract public function set(string $key, $value);

    abstract public function delete(string $key);

    abstract public function get(string $key);

    abstract public function exists(string $key);
}
