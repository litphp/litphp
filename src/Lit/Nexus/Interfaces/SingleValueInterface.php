<?php

declare(strict_types=1);

namespace Lit\Nexus\Interfaces;

interface SingleValueInterface extends ReadableSingleValueInterface
{

    /**
     * Update the value
     *
     * @param mixed $value
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
