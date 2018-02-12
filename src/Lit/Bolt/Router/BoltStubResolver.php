<?php namespace Lit\Bolt\Router;

use Interop\Http\Server\RequestHandlerInterface;
use Lit\Bolt\BoltContainer;
use Lit\Core\Interfaces\RouterStubResolverInterface;
use Nimo\Handlers\CallableHandler;
use Nimo\Handlers\FixedResponseHandler;
use Psr\Http\Message\ResponseInterface;

class BoltStubResolver implements RouterStubResolverInterface
{
    /**
     * @var BoltContainer
     */
    protected $container;

    public function __construct(BoltContainer $container)
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
