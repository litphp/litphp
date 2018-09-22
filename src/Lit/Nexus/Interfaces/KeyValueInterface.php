<?php

declare(strict_types=1);

namespace Lit\Nexus\Interfaces;

interface KeyValueInterface extends ReadableKeyValueInterface
{

    /**
     * Write the key with value
     * Supported value format is undefined. implementation is free to decide, and throw some Exception on illegal value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value);

    /**
     * Remove value in the key
     * The bahaviour of deleting unexist key is undefined, implementation is free to ignore it, or throw something out.
     *
     * @param string $key
     * @return void
     */
    public function delete($key);
}
