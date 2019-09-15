<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute\ArgumentHandler;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Write route arguments to request attributes.
 */
class ArgumentToAttribute implements ArgumentHandlerInterface
{
    public function attachArguments(
        ServerRequestInterface $request,
        array $routeArgs
    ): ServerRequestInterface {
        foreach ($routeArgs as $key => $val) {
            $request = $request->withAttribute($key, $val);
        }

        return $request;
    }
}
