<?php

declare(strict_types=1);

namespace Lit\Bolt\Router;

use Lit\Air\Configurator;
use Lit\Air\Factory;
use Lit\Air\Psr\Container;
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

        $handler = Configurator::convertToRecipe($stub)->resolve($this->container);

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
