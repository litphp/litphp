<?php

declare(strict_types=1);

namespace Lit\Bolt\Router;

use Lit\Air\Configurator;
use Lit\Air\Factory;
use Lit\Air\Psr\Container;
use Lit\Air\Utils;
use Lit\Nimo\Handlers\CallableHandler;
use Lit\Nimo\Handlers\FixedResponseHandler;
use Lit\Voltage\Interfaces\RouterStubResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Standard stub resolver using DI container to resolve stubs
 */
class BoltStubResolver implements RouterStubResolverInterface
{
    /**
     * @var Container
     */
    protected $container;
    /**
     * @var mixed
     */
    protected $notFound;

    public function __construct(Container $container, $notFound = null)
    {
        $this->container = $container;
        $this->notFound = $notFound;
    }

    /**
     * Resolve the stub and return a RequestHandler
     *
     * @param mixed $stub The stub value to be resolved.
     * @return RequestHandlerInterface
     */
    public function resolve($stub): RequestHandlerInterface
    {
        if ($stub === null && $this->notFound) {
            $stub = $this->notFound;
        }

        if (is_callable($stub)) {
            return new CallableHandler($stub);
        }

        if ($stub instanceof RequestHandlerInterface) {
            return $stub;
        }

        if ($stub instanceof ResponseInterface) {
            return FixedResponseHandler::wrap($stub);
        }

        //$className
        if (is_string($stub) && class_exists($stub) && is_subclass_of($stub, RequestHandlerInterface::class)) {
            $handler = Factory::of($this->container)->instantiate($stub);
            assert($handler instanceof RequestHandlerInterface);

            return $handler;
        }

        //[$className, $params]
        if (
            Utils::isSequentialArray($stub, 2)
            && is_string($stub[0])
            && class_exists($stub[0])
            && is_subclass_of($stub[0], RequestHandlerInterface::class)
        ) {
            $handler = Factory::of($this->container)->instantiate($stub[0], $stub[1]);
            assert($handler instanceof RequestHandlerInterface);

            return $handler;
        }

        /** @var Configurator $cfg */
        $cfg = $this->container::CONFIGURATOR_CLASS;
        $handler = $cfg::convertToRecipe($stub)->resolve($this->container);

        if (!$handler instanceof RequestHandlerInterface) {
            /** @var StubResolveException $exception */
            $exception = Factory::of($this->container)->instantiate(StubResolveException::class, [
                'stub' => $stub,
            ]);

            throw $exception;
        }

        return $handler;
    }
}
