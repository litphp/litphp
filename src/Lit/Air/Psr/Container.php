<?php

declare(strict_types=1);

namespace Lit\Air\Psr;

use Lit\Air\Configurator;
use Lit\Air\Factory;
use Lit\Air\Injection\InjectorInterface;
use Lit\Air\Recipe\AbstractRecipe;
use Lit\Air\Recipe\AliasRecipe;
use Lit\Air\Recipe\AutowireRecipe;
use Lit\Air\Recipe\BuilderRecipe;
use Lit\Air\Recipe\FixedValueRecipe;
use Lit\Air\Recipe\InstanceRecipe;
use Lit\Air\Recipe\RecipeInterface;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    const KEY_FACTORY = Factory::class;
    const KEY_INJECTORS = InjectorInterface::class;
    const CONFIGURATOR_CLASS = Configurator::class;
    /**
     * @var RecipeInterface[]
     */
    protected $recipe = [];
    protected $cache = [];

    /**
     * @var ContainerInterface
     */
    protected $delegateContainer;

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

    public static function alias(string $alias): AbstractRecipe
    {
        return new AliasRecipe($alias);
    }

    public static function autowire(?string $className = null, array $extra = []): AbstractRecipe
    {
        return new AutowireRecipe($className, $extra);
    }

    public static function instance(?string $className = null, array $extra = []): AbstractRecipe
    {
        return new InstanceRecipe($className, $extra);
    }

    public static function builder(callable $builder, array $extra = []): AbstractRecipe
    {
        return new BuilderRecipe($builder, $extra);
    }

    public static function value($value): AbstractRecipe
    {
        return new FixedValueRecipe($value);
    }

    public static function wrap(ContainerInterface $container): self
    {
        return (new static)->setDelegateContainer($container);
    }

    /**
     * @param string $id
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function get($id)
    {
        if (array_key_exists($id, $this->cache)) {
            return $this->cache[$id];
        }

        if (array_key_exists($id, $this->recipe)) {
            return $this->recipe[$id]->resolve($this, $id);
        }

        if ($id === static::KEY_FACTORY) {
            return $this->cache[$id] = new Factory($this);
        }

        if ($this->delegateContainer && $this->delegateContainer->has($id)) {
            return $this->delegateContainer->get($id);
        }

        throw new NotFoundException($id);
    }

    public function has($id)
    {
        return array_key_exists($id, $this->cache)
            || array_key_exists($id, $this->recipe)
            || $id === static::KEY_FACTORY
            || ($this->delegateContainer && $this->delegateContainer->has($id));
    }

    public function define(string $id, RecipeInterface $recipe): self
    {
        $this->recipe[$id] = $recipe;
        return $this;
    }

    public function getRecipe(string $id): ?RecipeInterface
    {
        if (array_key_exists($id, $this->recipe)) {
            return $this->recipe[$id];
        }

        return null;
    }

    public function extendRecipe(string $id, callable $wrapper): self
    {
        if (!array_key_exists($id, $this->recipe)) {
            throw new \InvalidArgumentException("recipe [$id] unexists");
        }

        $recipe = static::applyRecipeWrapper($wrapper, $this->recipe[$id]);

        $this->recipe[$id] = $recipe;

        return $this;
    }

    public function hasCacheEntry(string $id): bool
    {
        return array_key_exists($id, $this->cache);
    }

    public function flush(string $id): self
    {
        unset($this->cache[$id]);
        return $this;
    }

    public function addInjector(InjectorInterface $injector): self
    {
        if (!isset($this->cache[static::KEY_INJECTORS])) {
            $this->cache[static::KEY_INJECTORS] = [$injector];
        } else {
            $this->cache[static::KEY_INJECTORS][] = $injector;
        }

        return $this;
    }

    public function resolveRecipe($value)
    {
        $class = static::CONFIGURATOR_CLASS;
        /**
         * @see Configurator::convertArray()
         * @var Configurator $class
         */
        return $class::convertToRecipe($value)->resolve($this);
    }

    public function set($id, $value): self
    {
        $this->cache[$id] = $value;
        return $this;
    }

    /**
     * @param ContainerInterface $delegateContainer
     * @return $this
     */
    public function setDelegateContainer(ContainerInterface $delegateContainer): self
    {
        $this->delegateContainer = $delegateContainer;

        return $this;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * @param callable $wrapper
     * @param RecipeInterface $recipe
     * @return AbstractRecipe
     */
    protected static function applyRecipeWrapper(callable $wrapper, RecipeInterface $recipe): AbstractRecipe
    {
        $recipe = $wrapper($recipe);

        return $recipe;
    }
}