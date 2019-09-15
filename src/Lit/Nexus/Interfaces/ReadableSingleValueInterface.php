<?php

declare(strict_types=1);

namespace Lit\Nexus\Interfaces;

/**
 * ReadableSingleValueInterface represent a single value that can be read.
 */
interface ReadableSingleValueInterface
{
    /**
     * Read the value
     *
     * @return mixed
     */
    public function get();

    /**
     * Return whether this value exists
     *
     * @return boolean
     */
    public function exists();
}
