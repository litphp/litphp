<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute\ArgumentHandler;

use Psr\Http\Message\ServerRequestInterface;

interface ArgumentHandlerInterface
{
    public function attachArguments(ServerRequestInterface $request, array $routeArgs): ServerRequestInterface;
}
