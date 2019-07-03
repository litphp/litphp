<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Pipe or chain of middlewares. This middleware is designed to run multiple middlewares one by one.
 */
class MiddlewarePipe extends AbstractMiddleware implements RequestHandlerInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    protected $stack = [];
    protected $index;

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
        try {
            $this->index = 0;
            return $this->handle($this->request);
        } finally {
            $this->index = null;
        }
    }

    /**
     * @param ServerRequestInterface $request The request.
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->index === null) {
            throw new \BadMethodCallException('unexpected call to MiddlewarePipe::handle');
        }

        if (!isset($this->stack[$this->index])) {
            try {
                return $this->delegate($request);
            } finally {
                $this->index = null;
            }
        }

        return $this->stack[$this->index++]->process($request, $this);
    }
}
