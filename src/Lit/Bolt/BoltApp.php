<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Injection\SetterInjector;
use Lit\Bolt\Middlewares\RequestContext;
use Lit\Nimo\Middlewares\MiddlewarePipe;
use Lit\Voltage\App;
use Lit\Voltage\Interfaces\ThrowableResponseInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Bolt application
 */
class BoltApp extends App
{
    public const SETTER_INJECTOR = SetterInjector::class;
    /**
     * @var ?RequestContext
     */
    protected $requestContext;

    /**
     * Injector for RequestContext
     *
     * @param RequestContext|null $requestContext The RequestContext.
     * @return $this
     */
    public function injectRequestContext(?RequestContext $requestContext)
    {
        $this->requestContext = $requestContext;
        return $this;
    }

    protected function main(): ResponseInterface
    {
        try {
            $middleware = $this->middlewarePipe;
            if (isset($this->requestContext)) {
                $pipe = new MiddlewarePipe();
                $pipe->append($this->requestContext);
                $pipe->append($middleware);
                $middleware = $pipe;
            }

            return $middleware->process($this->request, $this->businessLogicHandler);
        } catch (ThrowableResponseInterface $e) {
            return $e->getResponse();
        }
    }
}
