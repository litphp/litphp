<?php

declare(strict_types=1);

namespace Nimo\Traits;

use Interop\Http\Server\MiddlewareInterface;
use Nimo\Interfaces\RequestPredictionInterface;
use Nimo\MiddlewarePipe;
use Nimo\Middlewares\CatchMiddleware;
use Nimo\Middlewares\PredictionWrapperMiddleware;

trait MiddlewareTrait
{
    use AttachToRequestTrait;

    /**
     * append $middleware after this one, return the new $middlewareStack
     *
     * @param $middleware
     * @return MiddlewarePipe
     */
    public function append(MiddlewareInterface $middleware): MiddlewarePipe
    {
        $stack = new MiddlewarePipe();

        return $stack
            ->append($this)
            ->append($middleware);
    }

    /**
     * prepend $middleware before this one, return the new $middlewareStack
     *
     * @param $middleware
     * @return MiddlewarePipe
     */
    public function prepend(MiddlewareInterface $middleware): MiddlewarePipe
    {
        $stack = new MiddlewarePipe();

        return $stack
            ->prepend($this)
            ->prepend($middleware);
    }

    public function when(RequestPredictionInterface $requestPrediction): MiddlewareInterface
    {
        return new PredictionWrapperMiddleware($this, $requestPrediction);
    }

    public function catch (callable $catcher, string $catchClass = \Throwable::class): MiddlewareInterface
    {
        return new CatchMiddleware($this, $catcher, $catchClass);
    }
}
