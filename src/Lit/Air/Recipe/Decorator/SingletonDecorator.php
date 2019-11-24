<?php

declare(strict_types=1);

namespace Lit\Air\Recipe\Decorator;

use Lit\Air\Psr\Container;

/**
 * Turns a recipe into a singleton. When it's resolved once, the value will be cached and reused.
 */
class SingletonDecorator extends AbstractRecipeDecorator
{
    protected $value;
    protected $isResolved = false;

    public function resolve(Container $container)
    {
        if (!$this->isResolved) {
            $this->value = $this->recipe->resolve($container);
            $this->isResolved = true;
        }

        return $this->value;
    }
}
