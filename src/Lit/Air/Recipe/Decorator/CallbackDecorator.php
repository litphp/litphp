<?php

declare(strict_types=1);

namespace Lit\Air\Recipe\Decorator;

use Psr\Container\ContainerInterface;

class CallbackDecorator extends AbstractRecipeDecorator
{
    public function resolve(ContainerInterface $container, ?string $id = null)
    {
        $delegate = function () use ($container, $id) {
            return $this->recipe->resolve($container, $id);
        };

        return call_user_func($this->option, $delegate, $container, $id);
    }
}
