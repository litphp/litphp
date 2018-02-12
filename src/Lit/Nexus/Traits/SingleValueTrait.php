<?php namespace Lit\Nexus\Traits;

use Lit\Nexus\Derived\FrozenValue;
use Lit\Nexus\Interfaces\SingleValueInterface;

trait SingleValueTrait
{
    public function freeze()
    {
        /**
         * @var SingleValueInterface $this ;
         */
        return FrozenValue::wrap($this);
    }
}
