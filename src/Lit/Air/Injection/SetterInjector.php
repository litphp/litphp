<?php

declare(strict_types=1);

namespace Lit\Air\Injection;

use Lit\Air\Configurator as C;
use Lit\Air\Factory;
use ReflectionMethod;

/**
 * SetterInjector inject dependencies into an object by it's setter method.
 * By default only method with name injectXXX and with single parameter will be considered.
 */
class SetterInjector implements InjectorInterface
{
    protected $prefixes = ['inject'];

    /**
     * SetterInjector constructor.
     *
     * @param array|string[] $prefixes Method prefix(es) to scan.
     */
    public function __construct(array $prefixes = ['inject'])
    {
        $this->prefixes = $prefixes;
    }

    public function inject(Factory $factory, $obj, array $extra = []): void
    {
        $class = new \ReflectionClass($obj);
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (!$this->shouldBeInjected($method)) {
                continue;
            }
            $parameter = $method->getParameters()[0];
            $paramClassName = null;
            $keys = [$parameter->name];
            $paramClass = $parameter->getClass();
            if (!empty($paramClass)) {
                $keys[] = $paramClassName = $paramClass->name;
            }

            $value = $factory->resolveDependency($class->name, $keys, $paramClassName, $extra);
            $method->invoke($obj, $value);
        }
    }

    public function isTarget($obj): bool
    {
        $class = get_class($obj);
        return defined("$class::SETTER_INJECTOR") && $class::SETTER_INJECTOR === static::class;
    }

    public function setPrefixes(array $prefixes): self
    {
        $this->prefixes = $prefixes;
        return $this;
    }

    protected function shouldBeInjected(ReflectionMethod $method)
    {
        if ($method->isStatic() || $method->isAbstract()) {
            return false;
        }
        $parameter = $method->getParameters();
        if (
            count($parameter) !== 1 || $parameter[0]->isOptional()
        ) {
            return false;
        }

        foreach ($this->prefixes as $prefix) {
            if (substr($method->name, 0, strlen($prefix)) === $prefix) {
                return true;
            }
        }
        return false;
    }

    /**
     * Configuration method of setter injector.
     *
     * @return array
     */
    public static function configuration()
    {
        return [
            Factory::CONFIG_INJECTORS => C::value([
                new SetterInjector(),
            ]),
        ];
    }
}
