<?php namespace Lit\Nexus\Interfaces;

interface SingleValueInterface extends ReadableSingleValueInterface
{

    /**
     * @param mixed $value
     * @return void
     */
    public function set($value);

    /**
     * @return void
     */
    public function delete();
}
