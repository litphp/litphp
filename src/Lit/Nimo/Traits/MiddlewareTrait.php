<?php

declare(strict_types=1);

namespace Lit\Nimo\Traits;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Trait MiddlewareTrait
 * @package Lit\Nimo\Traits
 */
trait MiddlewareTrait
{
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
}
