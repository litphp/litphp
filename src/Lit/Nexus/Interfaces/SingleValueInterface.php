<?php

declare(strict_types=1);

namespace Lit\Nexus\Interfaces;

/**
 * SingleValueInterface represent a single value that can be accessed.
 */
interface SingleValueInterface extends ReadableSingleValueInterface
{

    /**
     * Update the value
     *
     * @param mixed $value The value.
     * @return void
     */
    public function set($value);

    /**
     * Remove the value
     *
     * @return void
     */
    public function delete();
}
