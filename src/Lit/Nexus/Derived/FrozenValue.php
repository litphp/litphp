<?php

declare(strict_types=1);

namespace Lit\Nexus\Derived;

use Lit\Nexus\Interfaces\ReadableSingleValueInterface;
use Lit\Nexus\Interfaces\SingleValueInterface;

/**
 * Derived ReadableSingleValueInterface class from a SingleValueInterface. Makes it readonly.
 */
class FrozenValue implements ReadableSingleValueInterface
{
    /**
     * @var SingleValueInterface
     */
    protected $value;

    protected function __construct(SingleValueInterface $value)
    {
        $this->value = $value;
    }

    /**
     * Return a readonly instance of the given SingleValueInterface
     *
     * @param SingleValueInterface $value The SingleValueInterface object.
     * @return static
     */
    public static function wrap(SingleValueInterface $value)
    {
        if ($value instanceof static) {
            return $value;
        }
        return new static($value);
    }

    public function get()
    {
        return $this->value->get();
    }

    public function exists()
    {
        return $this->value->exists();
    }
}
