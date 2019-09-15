<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

use Lit\Air\Recipe\Decorator\CallbackDecorator;
use Lit\Air\Recipe\Decorator\SingletonDecorator;

/**
 * RecipeTrait provides some wrapping method to a recipe object.
 */
trait RecipeTrait
{
    public function singleton(): RecipeInterface
    {
        /**
         * @var RecipeInterface $this
         */
        return SingletonDecorator::decorate($this);
    }

    public function wrap(callable $callable): RecipeInterface
    {
        /**
         * @var RecipeInterface $this
         */
        return CallbackDecorator::decorate($this)->setOption($callable);
    }
}
