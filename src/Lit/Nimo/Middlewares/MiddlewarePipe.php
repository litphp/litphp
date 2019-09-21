<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Lit\Nimo\Handlers\PipeNextHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Pipe or chain of middlewares. This middleware is designed to run multiple middlewares one by one.
 */
class MiddlewarePipe extends AbstractMiddleware
{
    /**
     * @var MiddlewareInterface[]
     */
    protected $stack = [];

    /**
     * append $middleware
     * return $this
     * note this method would modify $this
     *
     * @param MiddlewareInterface $middleware The middleware to be append.
     * @return $this
     */
    public function append(MiddlewareInterface $middleware): MiddlewarePipe
    {
        $this->stack[] = $middleware;
        return $this;
    }

    /**
     * prepend $middleware
     * return $this
     * note this method would modify $this
     *
     * @param MiddlewareInterface $middleware The middleware to be prepend.
     * @return $this
     */
    public function prepend(MiddlewareInterface $middleware): MiddlewarePipe
    {
        array_unshift($this->stack, $middleware);
        return $this;
    }

    protected function main(): ResponseInterface
    {
        return $this->iterate($this->request, 0);
    }

    /**
     * This is a internal method for run one single iteration.
     *
     * @internal This is a public method for PipeNextHandler but NOT considered part of public API.
     *
     * @param ServerRequestInterface $request The request.
     * @param int                    $index   Current iteration index.
     * @return ResponseInterface
     */
    public function iterate(ServerRequestInterface $request, int $index): ResponseInterface
    {
        if (!isset($this->stack[$index])) {
            return $this->delegate($request);
        }

        return $this->stack[$index]->process($request, new PipeNextHandler($this, $index + 1));
    }
}
