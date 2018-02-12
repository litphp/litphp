<?php

declare(strict_types=1);

namespace Lit\Air\Recipe\Decorator;

use Lit\Air\WritableContainerInterface;

class SingletonDecorator extends AbstractRecipeDecorator
{
    protected $value;
    protected $isResolved = false;

    public function resolve(WritableContainerInterface $container, ?string $id = null)
    {
        if (!$this->isResolved) {
            $this->value = $this->recipe->resolve($container, $id);
            $this->isResolved = true;
        }

        return $this->value;
    }
}
