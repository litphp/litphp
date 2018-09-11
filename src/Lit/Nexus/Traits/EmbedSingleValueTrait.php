<?php

declare(strict_types=1);

namespace Lit\Nexus\Traits;

use Lit\Nexus\Interfaces\SingleValueInterface;

trait EmbedSingleValueTrait
{
    /**
     * @var SingleValueInterface
     */
    protected $innerSingleValue;

    public function get()
    {
        return $this->innerSingleValue->get();
    }

    public function delete()
    {
        $this->innerSingleValue->delete();
    }

    public function exists()
    {
        return $this->innerSingleValue->exists();
    }

    public function set($value)
    {
        $this->innerSingleValue->set($value);
    }

    /**
     * @return SingleValueInterface
     */
    public function getInnerSingleValue()
    {
        return $this->innerSingleValue;
    }
}
