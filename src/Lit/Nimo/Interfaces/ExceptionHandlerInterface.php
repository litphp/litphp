<?php

declare(strict_types=1);

namespace Lit\Nimo\Interfaces;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface ExceptionHandlerInterface
{
    public function handle(
        \Throwable $exception,
        ServerRequestInterface $request,
        RequestHandlerInterface $orignalHandler,
        MiddlewareInterface $originalMiddleware
    ): ResponseInterface;
}
