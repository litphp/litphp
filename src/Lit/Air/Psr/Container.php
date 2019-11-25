<?php

declare(strict_types=1);

namespace Lit\Air\Psr;

use Lit\Air\Configurator;
use Lit\Air\Recipe\AbstractRecipe;
use Lit\Air\Recipe\AliasRecipe;
use Lit\Air\Recipe\AutowireRecipe;
use Lit\Air\Recipe\BuilderRecipe;
use Lit\Air\Recipe\FixedValueRecipe;
use Lit\Air\Recipe\InstanceRecipe;
use Lit\Air\Recipe\RecipeInterface;
use Psr\Container\ContainerInterface;

/**
 * Air DI container
 */
class Container implements ContainerInterface
{
    public const CONFIGURATOR_CLASS = Configurator::class;
    /**
     * @var RecipeInterface[]
     */
    protected $recipe = [];
    protected $local = [];

    /**
     * @var ContainerInterface|null
     */
    protected $delegateContainer;

    /**
     * Container constructor.
     *
     * @param array|null $config Configuration array. See `Configurator` for more details.
     */
    public function __construct(?array $config = null)
    {
        if ($config) {
            $class = static::CONFIGURATOR_CLASS;
            /**
             * @see Configurator::config()
             * @var Configurator $class
             */
            $class::config($this, $config);
        }
        $this->set(static::class, $this);
        $this->set(ContainerInterface::class, $this);
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
     * Wraps a PSR container
     *
     * @param ContainerInterface $container The container.
     * @return Container
     */
    public static function wrap(ContainerInterface $container): self
    {
        return (new static())->setDelegateContainer($container);
    }

    public function get($id)
    {
        if (array_key_exists($id, $this->local)) {
            return $this->local[$id];
        }

        if (array_key_exists($id, $this->recipe)) {
            return $this->recipe[$id]->resolve($this);
        }

        if ($this->delegateContainer && $this->delegateContainer->has($id)) {
            return $this->delegateContainer->get($id);
        }

        throw new NotFoundException($id);
    }

    public function has($id)
    {
        return array_key_exists($id, $this->local)
            || array_key_exists($id, $this->recipe)
            || ($this->delegateContainer && $this->delegateContainer->has($id));
    }

    /**
     * Set a recipe to given id
     *
     * @param string          $id     The key.
     * @param RecipeInterface $recipe The recipe instance.
     * @return Container
     */
    public function define(string $id, RecipeInterface $recipe): self
    {
        $this->recipe[$id] = $recipe;
        return $this;
    }

    /**
     * Provide parameters for a class.
     * (Define an autowire recipe with in container with name of the class)
     *
     * @param string $className The Classname.
     * @param array  $extra     Extra parameteres.
     * @param bool   $cached    Whether to reuse the instance
     * @return Container
     */
    public function provideParameter(string $className, array $extra = [], bool $cached = true)
    {
        return $this->define($className, self::autowire($className, $extra, $cached));
    }

    /**
     * Get recipe instance from the id
     *
     * @param string $id The key.
     * @return RecipeInterface|null
     */
    public function getRecipe(string $id): ?RecipeInterface
    {
        return $this->recipe[$id] ?? null;
    }

    /**
     * Get a recipe from the id, wrap a new recipe to replace it.
     *
     * @param string   $id      The key.
     * @param callable $wrapper A recipe wrapper with signature (RecipeInterface): RecipeInterface.
     * @return Container
     */
    public function extendRecipe(string $id, callable $wrapper): self
    {
        if (!array_key_exists($id, $this->recipe)) {
            throw new \InvalidArgumentException("recipe [$id] unexists");
        }

        $recipe = static::applyRecipeWrapper($wrapper, $this->recipe[$id]);

        $this->recipe[$id] = $recipe;

        return $this;
    }

    /**
     * Detect if there is a local entry for id
     *
     * @param string $id The key.
     * @return boolean
     */
    public function hasLocalEntry(string $id): bool
    {
        return array_key_exists($id, $this->local);
    }

    /**
     * Remove the local entry in id. Note this will never touch recipe.
     *
     * @param string $id The key.
     * @return Container
     */
    public function flush(string $id): self
    {
        unset($this->local[$id]);
        return $this;
    }

    /**
     * Convert a value into recipe and resolve it. See
     * http://litphp.github.io/docs/air-config#structure-of-configuration
     *
     * @param mixed $value The value.
     * @return mixed
     */
    public function resolveRecipe($value)
    {
        $class = static::CONFIGURATOR_CLASS;
        /**
         * @see Configurator::convertArray()
         * @var Configurator $class
         */
        return $class::convertToRecipe($value)->resolve($this);
    }

    /**
     * Set a local entry
     *
     * @param string $id    The key.
     * @param mixed  $value The Value.
     * @return Container
     */
    public function set(string $id, $value): self
    {
        $this->local[$id] = $value;
        return $this;
    }

    /**
     * Set a delegate container.
     * https://github.com/container-interop/container-interop/blob/HEAD/docs/Delegate-lookup.md
     *
     * @param ContainerInterface $delegateContainer The delegate container.
     * @return $this
     */
    public function setDelegateContainer(ContainerInterface $delegateContainer): self
    {
        $this->delegateContainer = $delegateContainer;

        return $this;
    }

    protected static function applyRecipeWrapper(callable $wrapper, RecipeInterface $recipe): RecipeInterface
    {
        $recipe = $wrapper($recipe);

        return $recipe;
    }
}
