<?php

declare(strict_types=1);

namespace Lit\Nexus\Interfaces;

/**
 * KeyValueInterface represents an object with some inner value which can be accessed by string key.
 */
interface KeyValueInterface extends ReadableKeyValueInterface
{

    /**
     * Write the key with value
     * Supported value format is undefined. implementation is free to decide, and throw some Exception on illegal value
     *
     * @param string $key   The key.
     * @param mixed  $value The value.
     * @return void
     */
    public function set(string $key, $value);

    /**
     * Remove value in the key
     * The bahaviour of deleting unexist key is undefined, implementation is free to ignore it, or throw something out.
     *
     * @param string $key The key.
     * @return void
     */
    public function delete(string $key);
}
