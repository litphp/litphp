<?php

declare(strict_types=1);

namespace Lit\Bolt\Router;

use Lit\Core\Interfaces\RouterStubResolverInterface;
use Lit\Nimo\Handlers\CallableHandler;
use Lit\Nimo\Handlers\FixedResponseHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BoltStubResolver implements RouterStubResolverInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve($stub): RequestHandlerInterface
    {
        if (is_callable($stub)) {
            return new CallableHandler($stub);
        }

        if ($stub instanceof RequestHandlerInterface) {
            return $stub;
        }

        if ($stub instanceof ResponseInterface) {
            return FixedResponseHandler::wrap($stub);
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return BoltContainerStub::tryParse($stub)->instantiateFrom($this->container);
    }
}
