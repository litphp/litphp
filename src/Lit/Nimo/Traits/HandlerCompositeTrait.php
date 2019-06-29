<?php

declare(strict_types=1);

namespace Lit\Nimo\Traits;

use Lit\Nimo\Handlers\MiddlewareIncluedHandler;
use Lit\Nimo\Interfaces\ExceptionHandlerInterface;
use Lit\Nimo\Middlewares\NoopMiddleware;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

trait HandlerCompositeTrait
{
    public function includeMiddleware(MiddlewareInterface $middleware): MiddlewareIncluedHandler
    {
        /**
         * @var RequestHandlerInterface $this
         */
        return new MiddlewareIncluedHandler($this, $middleware);
    }

    public function catch(
        ExceptionHandlerInterface $catcher,
        string $catchClass = \Throwable::class
    ): MiddlewareIncluedHandler {
        return $this->includeMiddleware(NoopMiddleware::instance()->catch($catcher, $catchClass));
    }
}
