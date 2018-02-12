<?php namespace Lit\Nexus\Interfaces;

interface KeyValueInterface extends ReadableKeyValueInterface
{

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value);

    /**
     * @param string $key
     * @return void
     */
    public function delete($key);
}
