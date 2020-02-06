<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\Psr\Container;

/**
 * Always return a fixed value. Useful for test, and as a wrapper of arbitary value.
 */
class FixedValueRecipe extends AbstractRecipe
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function resolve(Container $container)
    {
        return $this->value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
