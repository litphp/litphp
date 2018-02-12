<?php

declare(strict_types=1);

namespace Nimo\Middlewares;

use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * User: mcfog
 * Date: 15/9/4
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

    abstract public function shouldRun(ServerRequestInterface $request, RequestHandlerInterface $handler): bool;
}
