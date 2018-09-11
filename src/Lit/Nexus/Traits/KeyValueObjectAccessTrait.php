<?php

declare(strict_types=1);

namespace Lit\Nexus\Traits;

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

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    abstract public function set($key, $value);

    /**
     * @param string $key
     * @return void
     */
    abstract public function delete($key);

    /**
     * @param string $key
     * @return mixed
     */
    abstract public function get($key);

    /**
     * @param string $key
     * @return bool
     */
    abstract public function exists($key);
}
