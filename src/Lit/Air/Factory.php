<?php

declare(strict_types=1);

namespace Lit\Air;

use Lit\Air\Injection\InjectorInterface;
use Lit\Air\Psr\CircularDependencyException;
use Lit\Air\Psr\Container;
use Lit\Air\Psr\ContainerException;
use Psr\Container\ContainerInterface;

/**
 * Air DI Factory
 */
class Factory
{
    public const CONTAINER_KEY = self::class;
    public const KEY_INJECTORS = self::class . '::injectors';
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var array
     */
    protected $circularStore = [];

    public function __construct(ContainerInterface $container = null)
    {
        if ($container instanceof Container) {
            $this->container = $container;
        } elseif ($container === null) {
            $this->container = new Container();
        } else {
            $this->container = Container::wrap($container);
        }
        $this->container->set(self::CONTAINER_KEY, $this);
    }

    /**
     * Get or create a factory instance from the container
     *
     * @param ContainerInterface $container The container.
     * @return Factory
     */
    public static function of(ContainerInterface $container): self
    {
        if (!$container->has(self::CONTAINER_KEY)) {
            return new self($container);
        }

        return $container->get(self::CONTAINER_KEY);
    }

    /**
     * Create a $className instance
     *
     * @param string $className       Requested classname.
     * @param array  $extraParameters Extra parameters.
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface Failure when fetching dependency in container.
     * @throws \ReflectionException Failure during reflection process.
     */
    public function instantiate(string $className, array $extraParameters = [])
    {
        $class = new \ReflectionClass($className);
        $constructor = $class->getConstructor();

        $constructParams = $constructor
            ? $this->resolveParams($constructor->getParameters(), $className, $extraParameters)
            : [];

        $instance = $class->newInstanceArgs($constructParams);
        $this->inject($instance, $extraParameters);

        return $instance;
    }

    /**
     * Call $callback with parameters injected
     *
     * @param callable $callback The callback to be called.
     * @param array    $extra    Extra parameters.
     * @return mixed
     */
    public function invoke(callable $callback, array $extra = [])
    {
        if (is_string($callback) || $callback instanceof \Closure) {
            $method = new \ReflectionFunction($callback);
            $params = $method->getParameters();
        } else {
            if (is_object($callback)) {
                $callback = [$callback, '__invoke'];
            }
            assert(is_array($callback));
            $method = (new \ReflectionClass($callback[0]))->getMethod($callback[1]);
            $params = $method->getParameters();
        }

        if ($method->isClosure()) {
            $name = sprintf('Closure@%s:%d', $method->getFileName(), $method->getStartLine());
        } elseif ($method instanceof \ReflectionMethod) {
            $name = sprintf('%s::%s', $method->getDeclaringClass()->name, $method->name);
        } else {
            assert($method instanceof \ReflectionFunction);
            preg_match('#function\s+([\w\\\\]+)#', (string)$method, $matches);
            $name = $matches[1];
        }

        assert(is_callable($callback));
        return call_user_func_array($callback, $this->resolveParams($params, '!' . $name, $extra));
    }

    /**
     * Produce a $className instance.
     *
     * @param string $className       Requested classname.
     * @param array  $extraParameters Extra parameters.
     * @param bool   $cached          Whether to save the instance if it's not defined in container.
     * @return object of $className
     * @throws \ReflectionException Failure during reflection process.
     */
    public function produce(string $className, array $extraParameters = [], bool $cached = true)
    {
        if (!class_exists($className)) {
            throw new \RuntimeException("$className not found");
        }

        if ($this->container->has($className)) {
            return $this->container->get($className);
        }

        $instance = $this->instantiate($className, $extraParameters);
        if ($cached) {
            $this->container->set($className, $instance);
        }

        return $instance;
    }

    /**
     * Run injectors on object. (Typically for setter injection)
     *
     * @param object $obj   The object to be injected.
     * @param array  $extra Extra parameters.
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface Failure when fetching dependency in container.
     */
    protected function inject($obj, array $extra = []): void
    {
        if (!$this->container->has(self::KEY_INJECTORS)) {
            return;
        }
        foreach ($this->container->get(self::KEY_INJECTORS) as $injector) {
            /**
             * @var InjectorInterface $injector
             */
            if ($injector->isTarget($obj)) {
                $injector->inject($this, $obj, $extra);
            }
        }
    }

    /**
     * Resolve a dependency item.
     * http://litphp.github.io/docs/air-di#working-on-dependencies
     *
     * @param string      $consumer  Represents who need the dependency. Often a class name.
     * @param array       $keys      Ordered array of string keys describing the dependency.
     * @param null|string $className Optional class name of the dependency.
     * @param array       $extra     Extra parameters.
     * @return mixed|object
     * @throws \Psr\Container\ContainerExceptionInterface Failure when fetching dependency in container.
     * @throws \ReflectionException Failure during reflection process.
     */
    public function resolveDependency(string $consumer, array $keys, ?string $className = null, array $extra = [])
    {
        if ($value = $this->produceFromClass($consumer, $keys, $extra)) {
            return $value[0];
        }

        if ($className && $this->container->has($className)) {
            return $this->container->get($className);
        }

        if ($className && class_exists($className)) {
            return $this->instantiate($className, $extra);
        }

        throw new ContainerException('failed to produce dependency for ' . $consumer);
    }

    /**
     * Register a injector.
     *
     * @param InjectorInterface $injector The injector.
     * @return Factory
     */
    public function addInjector(InjectorInterface $injector): self
    {
        if (!$this->container->has(static::KEY_INJECTORS)) {
            $this->container->set(static::KEY_INJECTORS, [$injector]);
        } else {
            $this->container->set(
                static::KEY_INJECTORS,
                array_merge($this->container->get(static::KEY_INJECTORS), [$injector])
            );
        }

        return $this;
    }

    /**
     * A subprocess of dependency resolving: try use candicate keys to find dependency.
     * http://litphp.github.io/docs/air-di#working-on-dependencies
     *
     * @param string $consumer Represents who need the dependency. Often a class name.
     * @param array  $keys     Ordered array of string keys describing the dependency.
     * @param array  $extra    Extra parameters.
     * @return array|null return single element array when success, null when fail, so null value can be handled
     * @throws \Psr\Container\ContainerExceptionInterface Failure when fetching dependency in container.
     */
    protected function produceFromClass(string $consumer, array $keys, array $extra = [])
    {
        if (!empty($extra) && ($value = $this->findFromArray($extra, $keys))) {
            return $value;
        }
        $current = $consumer;

        do {
            if (
                !empty($current)
                && $this->container->has("$current::")
                && ($value = $this->findFromArray($this->container->get("$current::"), $keys))
            ) {
                return $value;
            }
        } while ($current = get_parent_class($current));

        return null;
    }

    protected function findFromArray($arr, $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $arr)) {
                return [$this->container->resolveRecipe($arr[$key])];
            }
        }

        return null;
    }

    /**
     * Resolve array of ReflectionParameter into concrete values
     *
     * @param array  $params   Array of ReflectionParameter.
     * @param string $consumer Represents who need the dependency.
     * @param array  $extra    Extra parameters.
     * @return array
     */
    protected function resolveParams(array $params, string $consumer, array $extra = [])
    {
        return array_map(
            function (\ReflectionParameter $parameter) use ($consumer, $extra) {
                return $this->resolveParam($consumer, $parameter, $extra);
            },
            $params
        );
    }

    /**
     * Resolve a parameter (of callback, or constructor)
     *
     * @param string               $consumer  Represents who need the dependency.
     * @param \ReflectionParameter $parameter The ReflectionParameter.
     * @param array                $extra     Extra parameters.
     * @return mixed|object
     * @throws \Psr\Container\ContainerExceptionInterface Failure when fetching dependency in container.
     * @throws \ReflectionException Failure during reflection process.
     */
    protected function resolveParam(string $consumer, \ReflectionParameter $parameter, array $extra)
    {
        $hash = sprintf('%s#%d', $consumer, $parameter->getPosition());
        if (isset($this->circularStore[$hash])) {
            throw new CircularDependencyException(array_keys($this->circularStore));
        }

        try {
            $this->circularStore[$hash] = true;
            list($keys, $paramClassName) = $this->parseParameter($parameter);

            return $this->resolveDependency($consumer, $keys, $paramClassName, $extra);
        } catch (CircularDependencyException $e) {
            throw $e;
        } catch (ContainerException $e) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            }

            throw new ContainerException(
                sprintf('failed to produce constructor parameter "%s" for %s', $parameter->getName(), $consumer),
                0,
                $e
            );
        } finally {
            unset($this->circularStore[$hash]);
        }
    }

    protected function parseParameter(\ReflectionParameter $parameter)
    {
        $paramClassName = null;
        $keys = [$parameter->name];

        try {
            $paramClass = $parameter->getClass();
            if (!empty($paramClass)) {
                $keys[] = $paramClassName = $paramClass->name;
            }
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (\ReflectionException $e) {
            //ignore exception when $parameter is type hinting for interface
        }

        $keys[] = $parameter->getPosition();

        return [$keys, $paramClassName];
    }
}
