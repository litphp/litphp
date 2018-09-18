<?php

declare(strict_types=1);

namespace Lit\Nexus\Void;

use Lit\Nexus\Interfaces\SingleValueInterface;
use Lit\Nexus\Traits\SingleValueTrait;

/**
 * Class VoidSingleValue
 * @package Lit\Nexus\Void
 * @SuppressWarnings(PHPMD)
 */
class VoidSingleValue implements SingleValueInterface
{
    use SingleValueTrait;

    public function get()
    {
        throw new \RuntimeException('cannot get from void SingleValue');
    }

    public function exists()
    {
        return false;
    }

    public function set($value)
    {
        //noop
    }

    public function delete()
    {
        //noop
    }
}
