<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Base class for middleware wrapper that skip inner logic sometimes.
 */
abstract class AbstractConditionMiddleware extends AbstractWrapperMiddleware
{
    protected function main(): ResponseInterface
    {
        if ($this->shouldRun($this->request, $this->handler)) {
            $response = $this->innerMiddleware->process($this->request, $this->handler);
        } else {
            $response = $this->delegate();
        }

        return $response;
    }

    abstract protected function shouldRun(ServerRequestInterface $request, RequestHandlerInterface $handler): bool;
}
