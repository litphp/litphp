<?php

declare(strict_types=1);

namespace Lit\Air;

use Lit\Air\Psr\Container;
use Lit\Air\Psr\ContainerException;
use Lit\Air\Recipe\AutowireRecipe;
use Lit\Air\Recipe\BuilderRecipe;
use Lit\Air\Recipe\Decorator\AbstractRecipeDecorator;
use Lit\Air\Recipe\Decorator\CallbackDecorator;
use Lit\Air\Recipe\Decorator\SingletonDecorator;
use Lit\Air\Recipe\FixedValueRecipe;
use Lit\Air\Recipe\InstanceRecipe;
use Lit\Air\Recipe\RecipeInterface;

/**
 * Configurator helps to build an array configuration, and writes array configuration into a container.
 * http://litphp.github.io/docs/air-config
 */
class Configurator
{
    protected static $decorators = [
        'callback' => CallbackDecorator::class,
        'singleton' => SingletonDecorator::class,
    ];

    /**
     * Write a configuration array into a container
     *
     * @param Container $container The container.
     * @param array     $config    The configuration array.
     * @param boolean   $force     Whether overwrite existing values.
     * @return void
     */
    public static function config(Container $container, array $config, bool $force = true): void
    {
        foreach ($config as $key => $value) {
            if (!$force && $container->has($key)) {
                continue;
            }
            self::write($container, $key, $value);
        }
    }

    /**
     * Convert a mixed value into a recipe.
     *
     * @param mixed $value The value.
     * @return RecipeInterface
     */
    public static function convertToRecipe($value): RecipeInterface
    {
        if (is_object($value) && $value instanceof RecipeInterface) {
            return $value;
        }

        if (is_callable($value)) {
            return (new BuilderRecipe($value))->singleton();
        }

        if (is_array($value) && array_key_exists(0, $value) && !empty($value['$'])) {
            return self::makeRecipe($value);
        }

        if (self::isSequentialArray($value, 1) && is_string($value[0])) {
            return new AutowireRecipe($value[0], [], false);
        }

        if (self::isSequentialArray($value, 2) && is_string($value[0]) && class_exists($value[0])) {
            return new InstanceRecipe($value[0], $value[1]);
        }

        if (is_array($value)) {
            trigger_error("array should be wrapped with C::value", E_USER_NOTICE);
        }

        return Container::value($value);
    }

    /**
     * Configuration indicating a singleton
     *
     * @param string $classname The class name.
     * @param array  $extra     Extra parameters.
     * @return array
     */
    public static function singleton(string $classname, array $extra = []): array
    {
        return self::decorateSingleton(self::instance($classname, $extra));
    }

    /**
     * Decorate a configuration, makes it a singleton (\Lit\Air\Recipe\Decorator\SingletonDecorator)
     *
     * @param array $config The configuration.
     * @return array
     */
    public static function decorateSingleton(array $config): array
    {
        $config['decorator'] = $config['decorator'] ?? [];
        $config['decorator']['singleton'] = true;

        return $config;
    }

    /**
     * Decorate a configuration with provided callback
     *
     * @param array    $config   The configuration.
     * @param callable $callback The callback.
     * @return array
     */
    public static function decorateCallback(array $config, callable $callback): array
    {
        $config['decorator'] = $config['decorator'] ?? [];
        $config['decorator']['callback'] = $callback;

        return $config;
    }

    /**
     * Provide extra parameter for autowired entry. The key should be a valid class name.
     *
     * @param array $extra Extra parameters.
     * @return array
     * @deprecated
     */
    public static function provideParameter(array $extra = []): array
    {
        return [
            '$' => 'autowire',
            null,
            $extra,
        ];
    }

    /**
     * Configuration indicating an autowired entry.
     *
     * @param string $classname The class name.
     * @param array  $extra     Extra parameters.
     * @param bool   $cached    Whether to save the instance if it's not defined in container.
     * @return array
     */
    public static function produce(string $classname, array $extra = [], bool $cached = true): array
    {
        return [
            '$' => 'autowire',
            $classname,
            $extra,
            $cached,
        ];
    }

    /**
     * Configuration indicating an instance created by factory.
     *
     * @param string $classname The class name.
     * @param array  $extra     Extra parameters.
     * @return array
     */
    public static function instance(string $classname, array $extra = []): array
    {
        return [
            '$' => 'instance',
            $classname,
            $extra,
        ];
    }

    /**
     * Configuration indicating an alias
     *
     * @param string ...$key Multiple keys will be auto joined.
     * @return array
     */
    public static function alias(string ...$key): array
    {
        return [
            '$' => 'alias',
            self::join(...$key),
        ];
    }

    /**
     * Configuration wrapping a builder method
     *
     * @param callable $builder The builder method.
     * @param array    $extra   Extra parameters.
     * @return array
     */
    public static function builder(callable $builder, array $extra = []): array
    {
        return [
            '$' => 'builder',
            $builder,
            $extra,
        ];
    }

    /**
     * Configuration wraps an arbitary value. For arrays it's recommended to always wrap with this.
     *
     * @param mixed $value The value.
     * @return array
     */
    public static function value($value): array
    {
        return [
            '$' => 'value',
            $value,
        ];
    }

    protected static function isSequentialArray($val, int $expectedLength = null): bool
    {
        if (!is_array($val)) {
            return false;
        }

        $cnt = count($val);
        if ($cnt === 0) {
            return $expectedLength === null || $expectedLength === 0;
        }

        // non-empty sequential array must have zero index, fast exit for 99% assoc arrays
        if (!array_key_exists(0, $val)) {
            return false;
        }

        // fast exit for length assertion
        if ($expectedLength !== null && $expectedLength !== $cnt) {
            return false;
        }

        foreach (array_keys($val) as $k => $v) {
            if ($k !== $v) {
                return false;
            }
        }

        return true;
    }

    protected static function write(Container $container, $key, $value)
    {
        if (is_scalar($value) || is_resource($value)) {
            $container->set($key, $value);
            return;
        }

        if (
            substr($key, -2) === '::'
            && class_exists(substr($key, 0, -2))
        ) {
            $container->set($key, self::convertArray($value));
            return;
        }

        $recipe = self::convertToRecipe($value);

        if ($recipe instanceof FixedValueRecipe) {
            $container->set($key, $recipe->getValue());
        } else {
            $container->flush($key);
            $container->define($key, $recipe);
        }
    }

    protected static function convertArray(array $value): array
    {
        $result = [];
        foreach ($value as $k => $v) {
            $result[$k] = self::convertToRecipe($v);
            if ($result[$k] instanceof FixedValueRecipe) {
                $result[$k] = $result[$k]->getValue();
            }
        }

        return $result;
    }

    protected static function makeRecipe(array $value): RecipeInterface
    {
        $type = $value['$'];
        unset($value['$']);

        if (
            array_key_exists($type, [
            'alias' => 1,
            'autowire' => 1,
            'instance' => 1,
            'builder' => 1,
            'value' => 1,
            ])
        ) {
            $valueDecorator = $value['decorator'] ?? null;
            unset($value['decorator']);

            $builder = [Container::class, $type];
            assert(is_callable($builder));
            /**
             * @var RecipeInterface $recipe
             */
            $recipe = call_user_func_array($builder, $value);

            if ($valueDecorator) {
                $recipe = self::wrapRecipeWithDecorators($valueDecorator, $recipe);
            }

            return $recipe;
        }

        throw new ContainerException("cannot understand given recipe");
    }

    /**
     * Apply decorators to a recipe and return the decorated recipe
     *
     * @param array           $decorators Assoc array of decorator names => options.
     * @param RecipeInterface $recipe     The decorated recipe instance.
     * @return RecipeInterface
     */
    public static function wrapRecipeWithDecorators(array $decorators, RecipeInterface $recipe): RecipeInterface
    {
        foreach ($decorators as $name => $option) {
            if (isset(self::$decorators[$name])) {
                $decorateFn = [self::$decorators[$name], 'decorate'];
                assert(is_callable($decorateFn));
                $recipe = call_user_func($decorateFn, $recipe);
            } elseif (is_subclass_of($name, AbstractRecipeDecorator::class)) {
                $decorateFn = [$name, 'decorate'];
                assert(is_callable($decorateFn));
                $recipe = call_user_func($decorateFn, $recipe);
            } else {
                throw new ContainerException("cannot understand recipe decorator [$name]");
            }

            assert($recipe instanceof AbstractRecipeDecorator);
            if (!empty($option)) {
                $recipe->setOption($option);
            }
        }

        return $recipe;
    }

    /**
     * Join multiple strings with air conventional separator `::`
     *
     * @param string ...$args Parts of the key to be joined.
     * @return string
     */
    public static function join(string ...$args): string
    {
        return implode('::', $args);
    }
}
