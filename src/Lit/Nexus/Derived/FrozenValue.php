<?php namespace Lit\Nexus\Derived;

use Lit\Nexus\Interfaces\ReadableSingleValueInterface;
use Lit\Nexus\Interfaces\SingleValueInterface;

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
     * @param SingleValueInterface $value
     * @return static
     */
    public static function wrap(SingleValueInterface $value)
    {
        if ($value instanceof static) {
            return $value;
        }
        return new static($value);
    }

    /**
     * @return mixed
     */
    public function get()
    {
        return $this->value->get();
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return $this->value->exists();
    }
}
