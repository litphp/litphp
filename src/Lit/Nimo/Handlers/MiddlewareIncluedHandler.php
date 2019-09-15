<?php

declare(strict_types=1);

namespace Lit\Nimo\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Wraps a handler by given middleware.
 */
class MiddlewareIncluedHandler extends AbstractWrapperHandler
{
    /**
     * @var MiddlewareInterface
     */
    protected $middleware;

    public function __construct(RequestHandlerInterface $handler, MiddlewareInterface $middleware)
    {
        parent::__construct($handler);
        $this->middleware = $middleware;
    }

    protected function main(): ResponseInterface
    {
        return $this->middleware->process($this->request, $this->innerHandler);
    }
}
