<?php

declare(strict_types=1);

namespace Lit\Nexus\Interfaces;

interface ReadableKeyValueInterface
{
    /**
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     * @return bool
     */
    public function exists($key);
}
