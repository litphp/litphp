<?php

declare(strict_types=1);

namespace Lit\Nimo\Traits;

use Lit\Nimo\Handlers\MiddlewareIncluedHandler;
use Lit\Nimo\Middlewares\NoopMiddleware;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

trait HandlerTrait
{
    public function includeMiddleware(MiddlewareInterface $middleware): RequestHandlerInterface
    {
        /**
         * @var RequestHandlerInterface $this
         */
        return new MiddlewareIncluedHandler($this, $middleware);
    }

    public function catch(callable $catcher, string $catchClass = \Throwable::class): RequestHandlerInterface
    {
        return $this->includeMiddleware(NoopMiddleware::instance()->catch($catcher, $catchClass));
    }
}
