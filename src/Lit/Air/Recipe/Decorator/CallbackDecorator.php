<?php

declare(strict_types=1);

namespace Lit\Air\Recipe\Decorator;

use Lit\Air\Psr\Container;

/**
 * Dynamic decorator that calls a callback (option value)
 */
class CallbackDecorator extends AbstractRecipeDecorator
{
    public function resolve(Container $container)
    {
        $delegate = function () use ($container) {
            return $this->recipe->resolve($container);
        };

        return call_user_func($this->option, $delegate, $container);
    }
}
