<?php namespace Lit\Nexus\Traits;

/**
 * make IKeyValue accessible via \ArrayAccess
 *
 * @package Lit\Nexus\Traits
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
