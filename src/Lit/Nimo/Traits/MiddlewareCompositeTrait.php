<?php

declare(strict_types=1);

namespace Lit\Nimo\Traits;

use Lit\Nimo\Interfaces\RequestPredictionInterface;
use Lit\Nimo\Middlewares\CatchMiddleware;
use Lit\Nimo\Middlewares\MiddlewarePipe;
use Lit\Nimo\Middlewares\PredictionWrapperMiddleware;
use Psr\Http\Server\MiddlewareInterface;

trait MiddlewareCompositeTrait
{
    /**
     * append $middleware after this one, return the new $middlewareStack
     *
     * @param MiddlewareInterface $middleware
     * @return MiddlewarePipe
     */
    public function append(MiddlewareInterface $middleware): MiddlewarePipe
    {
        $stack = new MiddlewarePipe();

        /** @noinspection PhpParamsInspection */
        return $stack
            ->append($this)
            ->append($middleware);
    }

    /**
     * prepend $middleware before this one, return the new $middlewareStack
     *
     * @param MiddlewareInterface $middleware
     * @return MiddlewarePipe
     */
    public function prepend(MiddlewareInterface $middleware): MiddlewarePipe
    {
        $stack = new MiddlewarePipe();

        /** @noinspection PhpParamsInspection */
        return $stack
            ->prepend($this)
            ->prepend($middleware);
    }

    public function when(RequestPredictionInterface $requestPrediction): PredictionWrapperMiddleware
    {
        /** @noinspection PhpParamsInspection */
        return new PredictionWrapperMiddleware($this, $requestPrediction);
    }

    public function unless(RequestPredictionInterface $requestPrediction): PredictionWrapperMiddleware
    {
        /** @noinspection PhpParamsInspection */
        return new PredictionWrapperMiddleware($this, $requestPrediction, true);
    }

    public function catch(callable $catcher, string $catchClass = \Throwable::class): CatchMiddleware
    {
        return new CatchMiddleware($this, $catcher, $catchClass);
    }
}
