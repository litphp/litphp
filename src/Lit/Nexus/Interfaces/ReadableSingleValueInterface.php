<?php namespace Lit\Nexus\Interfaces;

interface ReadableSingleValueInterface
{
    /**
     * @return mixed
     */
    public function get();

    /**
     * @return bool
     */
    public function exists();
}
