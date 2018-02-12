<?php

declare(strict_types=1);

namespace Nimo;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Nimo\Traits\MiddlewareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * User: mcfog
 * Date: 15/9/4
 */
abstract class AbstractMiddleware implements MiddlewareInterface
{
    use MiddlewareTrait;
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var RequestHandlerInterface
     */
    protected $handler;


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->request = $request;
        $this->handler = $handler;

        return $this->main();
    }

    /**
     * @return ResponseInterface
     */
    abstract protected function main(): ResponseInterface;

    protected function delegate(ServerRequestInterface $request = null): ResponseInterface
    {
        return $this->handler->handle($request ?: $this->request);
    }

    protected function invokeCallback(callable $callback)
    {
        return call_user_func($callback, $this->request, $this->handler);
    }
}
