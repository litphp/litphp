<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Psr\Container\ContainerInterface;

class FixedValueRecipe extends AbstractRecipe
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function resolve(ContainerInterface $container, ?string $id = null)
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
