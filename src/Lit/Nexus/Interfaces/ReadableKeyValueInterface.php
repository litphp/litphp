<?php

declare(strict_types=1);

namespace Lit\Nexus\Interfaces;

interface ReadableKeyValueInterface
{
    /**
     * Read the value in key and return that.
     * Behaviour of read an unexisting key is undefined.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Return whether the key exists
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key);
}
