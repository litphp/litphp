<?php

declare(strict_types=1);

namespace Lit\Air\Recipe\Decorator;

use Lit\Air\WritableContainerInterface;

class CacheDecorator extends AbstractRecipeDecorator
{
    public function resolve(WritableContainerInterface $container, ?string $id = null)
    {
        $value = $this->recipe->resolve($container, $id);
        if (!is_null($id)) {
            $container->set($id, $value);
        }

        return $value;
    }
}
