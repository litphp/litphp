<?php

declare(strict_types=1);

namespace Lit\Nexus\Void;

use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Traits\KeyValueTrait;

/**
 * Class VoidKeyValue
 * @package Lit\Nexus\Void
 * @SuppressWarnings(PHPMD)
 */
class VoidKeyValue implements KeyValueInterface
{
    use KeyValueTrait;

    public function set(string $key, $value)
    {
        //noop
    }

    public function delete(string $key)
    {
        //noop
    }

    public function get(string $key)
    {
        throw new \RuntimeException('cannot get from void KeyValue');
    }

    public function exists(string $key)
    {
        return false;
    }
}
