<?php

declare(strict_types=1);

namespace Lit\Nimo\Traits;

use Lit\Nimo\Interfaces\RequestPredictionInterface;
use Lit\Nimo\MiddlewarePipe;
use Lit\Nimo\Middlewares\CatchMiddleware;
use Lit\Nimo\Middlewares\PredictionWrapperMiddleware;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Trait MiddlewareTrait
 * @package Lit\Nimo\Traits
 * @mixin MiddlewareInterface
 */
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
