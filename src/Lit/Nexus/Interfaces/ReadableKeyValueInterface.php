<?php

declare(strict_types=1);

namespace Lit\Nexus\Interfaces;

/**
 * ReadableKeyValueInterface represents an object with some inner value which can be read by string key.
 */
interface ReadableKeyValueInterface
{
    /**
     * Read the value in key and return that.
     * Behaviour of read an unexisting key is undefined.
     *
     * @param string $key The key.
     * @return mixed
     */
    public function get(string $key);

    /**
     * Return whether the key exists
     *
     * @param string $key The key.
     * @return boolean
     */
    public function exists(string $key);
}
