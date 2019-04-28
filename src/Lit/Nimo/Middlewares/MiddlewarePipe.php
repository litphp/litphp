<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Lit\Nimo\Handlers\CallableHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

class MiddlewarePipe extends AbstractMiddleware
{
    /**
     * @var CallableHandler
     */
    protected $nextHandler;
    /**
     * @var MiddlewareInterface[]
     */
    protected $stack = [];
    protected $index;

    public function __construct()
    {
        $this->nextHandler = CallableHandler::wrap(function (ServerRequestInterface $request) {
            return $this->loop($request);
        });
    }


    /**
     * append $middleware
     * return $this
     * note this method would modify $this
     *
     * @param MiddlewareInterface $middleware
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
     * @param MiddlewareInterface $middleware
     * @return $this
     */
    public function prepend(MiddlewareInterface $middleware): MiddlewarePipe
    {
        array_unshift($this->stack, $middleware);
        return $this;
    }

    protected function main(): ResponseInterface
    {
        $this->index = 0;

        return $this->loop($this->request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function loop(ServerRequestInterface $request): ResponseInterface
    {
        if (!isset($this->stack[$this->index])) {
            return $this->delegate($request);
        }

        return $this->stack[$this->index++]->process($request, $this->nextHandler);
    }
}
