<?php

declare(strict_types=1);

namespace Lit\Air;

use Lit\Air\Psr\Container;
use Lit\Air\Psr\ContainerException;
use Lit\Air\Recipe\BuilderRecipe;
use Lit\Air\Recipe\Decorator\AbstractRecipeDecorator;
use Lit\Air\Recipe\Decorator\CallbackDecorator;
use Lit\Air\Recipe\Decorator\SingletonDecorator;
use Lit\Air\Recipe\FixedValueRecipe;
use Lit\Air\Recipe\RecipeInterface;

class Configurator
{
    protected static $decorators = [
        'callback' => CallbackDecorator::class,
        'singleton' => SingletonDecorator::class,
    ];

    public static function config(Container $container, array $config, bool $force = true): void
    {
        foreach ($config as $key => $value) {
            if (!$force && $container->has($key)) {
                continue;
            }
            self::write($container, $key, $value);
        }
    }

    public static function convertToRecipe($value): RecipeInterface
    {
        if (is_object($value) && $value instanceof RecipeInterface) {
            return $value;
        }

        if (is_callable($value)) {
            return (new BuilderRecipe($value))->singleton();
        }

        if (is_array($value) && array_key_exists(0, $value) && isset($value['$'])) {
            return self::makeRecipe($value);
        }

        return Container::value($value);
    }

    public static function singleton(string $classname, array $extra = []): array
    {
        return self::decorateSingleton(self::instance($classname, $extra));
    }

    public static function decorateSingleton(array $config):array
    {
        $config['decorator'] = $config['decorator'] ?? [];
        $config['decorator']['singleton'] = true;

        return $config;
    }

    public static function decorateCallback(array $config, callable $callback):array
    {
        $config['decorator'] = $config['decorator'] ?? [];
        $config['decorator']['callback'] = $callback;

        return $config;
    }

    public static function provideParameter(array $extra = []): array
    {
        return [
            '$' => 'autowire',
            null,
            $extra,
        ];
    }

    public static function produce(?string $classname, array $extra = []): array
    {
        return [
            '$' => 'autowire',
            $classname,
            $extra,
        ];
    }

    public static function instance(string $classname, array $extra = []): array
    {
        return [
            '$' => 'instance',
            $classname,
            $extra,
        ];
    }

    public static function alias(string... $key): array
    {
        return [
            '$' => 'alias',
            self::join(...$key),
        ];
    }

    public static function builder(callable $builder): array
    {
        return [
            '$' => 'builder',
            $builder,
        ];
    }

    public static function value($value): array
    {
        return [
            '$' => 'value',
            $value,
        ];
    }

    protected static function write(Container $container, $key, $value)
    {
        if (is_scalar($value) || is_resource($value)) {
            $container->set($key, $value);
            return;
        }

        if (substr($key, -2) === '::'
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

    /**
     * @param array $value
     * @return array
     */
    protected static function convertArray(array $value): array
    {
        $result = [];
        foreach ($value as $k => $v) {
            if (is_scalar($v) || is_resource($v)) {
                $result[$k] = $v;
            } else {
                $result[$k] = self::convertToRecipe($v);
                if ($result[$k] instanceof FixedValueRecipe) {
                    $result[$k] = $result[$k]->getValue();
                }
            }
        }

        return $result;
    }

    protected static function makeRecipe(array $value): RecipeInterface
    {
        $type = $value['$'];
        unset($value['$']);

        if (array_key_exists($type, [
            'alias' => 1,
            'autowire' => 1,
            'instance' => 1,
            'builder' => 1,
            'value' => 1,
        ])) {
            $valueDecorator = $value['decorator'] ?? null;
            unset($value['decorator']);

            /**
             * @var RecipeInterface $recipe
             */
            $recipe = call_user_func_array([Container::class, $type], $value);

            if ($valueDecorator) {
                $recipe = self::wrapRecipeWithDecorators($valueDecorator, $recipe);
            }

            return $recipe;
        }

        throw new ContainerException("cannot understand given recipe");
    }

    /**
     * @param array $decorators
     * @param RecipeInterface $recipe
     * @return RecipeInterface
     */
    public static function wrapRecipeWithDecorators(array $decorators, RecipeInterface $recipe): RecipeInterface
    {
        foreach ($decorators as $name => $option) {
            if (isset(self::$decorators[$name])) {
                $recipe = call_user_func([self::$decorators[$name], 'decorate'], $recipe);
            } elseif (is_subclass_of($name, AbstractRecipeDecorator::class)) {
                $recipe = call_user_func([$name, 'decorate'], $recipe);
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

    public static function join(string... $args): string
    {
        return implode('::', $args);
    }
}
