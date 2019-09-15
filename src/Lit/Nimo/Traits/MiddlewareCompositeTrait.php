<?php

declare(strict_types=1);

namespace Lit\Nimo\Traits;

use Lit\Nimo\Interfaces\ExceptionHandlerInterface;
use Lit\Nimo\Interfaces\RequestPredictionInterface;
use Lit\Nimo\Middlewares\CatchMiddleware;
use Lit\Nimo\Middlewares\MiddlewarePipe;
use Lit\Nimo\Middlewares\PredictionWrapperMiddleware;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Inludes some wrapping and compositing method about middleware.
 */
trait MiddlewareCompositeTrait
{
    /**
     * append $middleware after this one, return the new $middlewareStack
     *
     * @param MiddlewareInterface $middleware The middleware.
     * @return MiddlewarePipe
     */
    public function append(MiddlewareInterface $middleware): MiddlewarePipe
    {
        $stack = new MiddlewarePipe();

        /** @var MiddlewareInterface $this */
        return $stack
            ->append($this)
            ->append($middleware);
    }

    /**
     * prepend $middleware before this one, return the new $middlewareStack
     *
     * @param MiddlewareInterface $middleware The middleware.
     * @return MiddlewarePipe
     */
    public function prepend(MiddlewareInterface $middleware): MiddlewarePipe
    {
        $stack = new MiddlewarePipe();

        /** @var MiddlewareInterface $this */
        return $stack
            ->prepend($this)
            ->prepend($middleware);
    }

    /**
     * return a new middleware that only take effect when the prediction is passed.
     *
     * @param RequestPredictionInterface $requestPrediction The prediction.
     * @return PredictionWrapperMiddleware
     */
    public function when(RequestPredictionInterface $requestPrediction): PredictionWrapperMiddleware
    {
        /** @var MiddlewareInterface $this */
        return new PredictionWrapperMiddleware($this, $requestPrediction);
    }

    /**
     * return a new middleware that only take effect when the prediction is NOT passed.
     *
     * @param RequestPredictionInterface $requestPrediction The prediction.
     * @return PredictionWrapperMiddleware
     */
    public function unless(RequestPredictionInterface $requestPrediction): PredictionWrapperMiddleware
    {
        /** @var MiddlewareInterface $this */
        return new PredictionWrapperMiddleware($this, $requestPrediction, true);
    }

    /**
     * wraps this middleware by try catch.
     *
     * @param ExceptionHandlerInterface $catcher    The exception handler.
     * @param string                    $catchClass The exception type to be caught.
     * @return CatchMiddleware
     */
    public function catch(ExceptionHandlerInterface $catcher, string $catchClass = \Throwable::class): CatchMiddleware
    {
        return new CatchMiddleware($this, $catcher, $catchClass);
    }
}
