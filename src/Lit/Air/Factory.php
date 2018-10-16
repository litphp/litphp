<?php

declare(strict_types=1);

namespace Lit\Air;

use Lit\Air\Injection\InjectorInterface;
use Lit\Air\Psr\CircularDependencyException;
use Lit\Air\Psr\Container;
use Lit\Air\Psr\ContainerException;
use Psr\Container\ContainerInterface;

class Factory implements ContainerInterface
{
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
        } elseif (is_null($container)) {
            $this->container = new Container();
        } else {
            $this->container = Container::wrap($container);
        }
        $this->container->set(Container::KEY_FACTORY, $this);
    }

    public static function of(ContainerInterface $container): self
    {
        if (!$container->has(Container::KEY_FACTORY)) {
            return new self($container);
        }

        return $container->get(Container::KEY_FACTORY);
    }

    /**
     * @param string $className
     * @param array $extraParameters
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \ReflectionException
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
     * @param callable $callback
     * @param array $extra
     * @return mixed
     * @throws \ReflectionException
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
            $method = (new \ReflectionClass($callback[0]))->getMethod($callback[1]);
            $params = $method->getParameters();
        }

        if ($method->isClosure()) {
            $name = sprintf('Closure@%s:%d', $method->getFileName(), $method->getStartLine());
        } elseif ($method instanceof \ReflectionMethod) {
            $name = sprintf('Method@%s::%s', $method->getDeclaringClass()->name, $method->name);
        } else {
            $name = (string)$method;
            $name = substr($name, 0, strpos($name, "{\n"));
        }

        return call_user_func_array($callback, $this->resolveParams($params, '!' . $name, $extra));
    }

    /**
     * @param string $className
     * @param array $extraParameters
     * @return object of $className«
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \ReflectionException
     */
    public function produce(string $className, array $extraParameters = [])
    {
        if ($this->container->hasCacheEntry($className)) {
            return $this->container->get($className);
        }

        if (!class_exists($className)) {
            throw new \RuntimeException("$className not found");
        }

        $instance = $this->instantiate($className, $extraParameters);

        $this->container->set($className, $instance);

        return $instance;
    }

    /**
     * @param string $className
     * @return object of $className
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \ReflectionException
     */
    public function getOrProduce(string $className)
    {
        $recipe = $this->container->getRecipe($className);
        if ($recipe) {
            return $this->container->get($className);
        }
        return $this->produce($className);
    }

    /**
     * @param object $obj
     * @param array $extra
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function inject($obj, array $extra = []): void
    {
        if (!$this->container->has(Container::KEY_INJECTORS)) {
            return;
        }
        foreach ($this->container->get(Container::KEY_INJECTORS) as $injector) {
            /**
             * @var InjectorInterface $injector
             */
            if ($injector->isTarget($obj)) {
                $injector->inject($this, $obj, $extra);
            }
        }
    }

    /**
     * @param $className
     * @param array $keys
     * @param null|string $dependencyClassName
     * @param array $extra
     * @return mixed|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \ReflectionException
     */
    public function resolveDependency($className, array $keys, ?string $dependencyClassName = null, array $extra = [])
    {
        if ($value = $this->produceFromClass($className, $keys, $extra)) {
            return $value[0];
        }

        if ($dependencyClassName && $this->container->has($dependencyClassName)) {
            return $this->container->get($dependencyClassName);
        }

        if (isset($dependencyClassName) && class_exists($dependencyClassName)) {
            return $this->instantiate($dependencyClassName);
        }

        throw new ContainerException('failed to produce dependency');
    }

    public function get($id)
    {
        if (!$this->has($id)) {
            throw new ContainerException('unknown class ' . $id);
        }

        return $this->getOrProduce($id);
    }

    public function has($id)
    {
        return class_exists($id);
    }

    /**
     * @param string $className
     * @param array $keys
     * @param array $extra
     * @return array|null
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    protected function produceFromClass(string $className, array $keys, array $extra = [])
    {
        $currentClassName = $className;
        if (!empty($extra) && ($value = $this->findFromArray($extra, $keys))) {
            return $value;
        }

        do {
            if (class_exists($currentClassName)
                && $this->container->has("$currentClassName::")
                && ($value = $this->findFromArray($this->container->get("$currentClassName::"), $keys))
            ) {
                return $value;
            }
        } while ($currentClassName = get_parent_class($currentClassName));

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

    protected function resolveParams(array $params, string $className, array $extra = [])
    {
        return array_map(
            function (\ReflectionParameter $parameter) use ($className, $extra) {
                return $this->resolveParam($className, $parameter, $extra);
            },
            $params
        );
    }

    /**
     * @param $className
     * @param \ReflectionParameter $parameter
     * @param array $extraParameters
     * @return mixed|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \ReflectionException
     */
    protected function resolveParam($className, \ReflectionParameter $parameter, array $extraParameters)
    {
        $hash = sprintf('%s#%d', $className, $parameter->getPosition());
        if (isset($this->circularStore[$hash])) {
            throw new CircularDependencyException(array_keys($this->circularStore));
        }

        try {
            $this->circularStore[$hash] = true;
            list($keys, $paramClassName) = $this->parseParameter($parameter);

            return $this->resolveDependency($className, $keys, $paramClassName, $extraParameters);
        } catch (CircularDependencyException $e) {
            throw $e;
        } catch (ContainerException $e) {
            if ($parameter->isOptional()) {
                return $parameter->getDefaultValue();
            }

            throw new ContainerException(
                sprintf('failed to produce constructor parameter "%s" for %s', $parameter->getName(), $className),
                0,
                $e
            );
        } finally {
            unset($this->circularStore[$hash]);
        }
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return array
     */
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
