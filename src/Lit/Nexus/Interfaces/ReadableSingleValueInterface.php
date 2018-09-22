<?php

declare(strict_types=1);

namespace Lit\Nexus\Interfaces;

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
     * @return bool
     */
    public function exists();
}
