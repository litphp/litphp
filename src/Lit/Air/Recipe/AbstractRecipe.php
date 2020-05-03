<?php

declare(strict_types=1);

namespace Lit\Air\Recipe;

/**
 * Base class of recipe
 */
abstract class AbstractRecipe implements RecipeInterface
{
    use RecipeTrait;

    /**
     * A fixed value
     *
     * @param mixed $value The value.
     * @return AbstractRecipe
     */
    public static function value($value): AbstractRecipe
    {
        return new FixedValueRecipe($value);
    }

    /**
     * Create a alias
     *
     * @param string $alias The alias string key.
     * @return AbstractRecipe
     */
    public static function alias(string $alias): AbstractRecipe
    {
        return new AliasRecipe($alias);
    }

    /**
     * Calls a builder method using factory.
     *
     * @param callable $builder The builder method. Its parameter will be injected as dependency.
     * @param array    $extra   Extra parameters.
     * @return AbstractRecipe
     */
    public static function builder(callable $builder, array $extra = []): AbstractRecipe
    {
        return new BuilderRecipe($builder, $extra);
    }

    /**
     * Populate an instance by factory
     *
     * @param string|null $className Optional classname. Can be ommited when the entry key is the classname.
     * @param array       $extra     Extra parameteres.
     * @return AbstractRecipe
     */
    public static function instance(?string $className = null, array $extra = []): AbstractRecipe
    {
        return new InstanceRecipe($className, $extra);
    }

    /**
     * Autowire this entry
     *
     * @param string $className The Classname.
     * @param array  $extra     Extra parameteres.
     * @param bool   $cached    Whether to save the instance if it's not defined in container.
     * @return AbstractRecipe
     */
    public static function autowire(string $className, array $extra = [], bool $cached = true): AbstractRecipe
    {
        return new AutowireRecipe($className, $extra, $cached);
    }
}
