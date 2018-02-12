<?php

declare(strict_types=1);

namespace Nimo\Traits;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Nimo\Handlers\MiddlewareIncluedHandler;

trait HandlerTrait
{
    public function includeMiddleware(MiddlewareInterface $middleware): RequestHandlerInterface
    {
        /**
         * @var RequestHandlerInterface $this
         */
        return new MiddlewareIncluedHandler($this, $middleware);
    }
}
