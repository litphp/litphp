<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute\ArgumentHandler;

use Lit\Nexus\Interfaces\ReadableKeyValueInterface;
use Lit\Nimo\Traits\AttachToRequestTrait;
use Psr\Http\Message\ServerRequestInterface;

class RouteArgumentBag implements ReadableKeyValueInterface, ArgumentHandlerInterface
{
    use AttachToRequestTrait;
    protected $routeArgs;

    public function attachArguments(
        ServerRequestInterface $request,
        array $routeArgs
    ): ServerRequestInterface {
        $bag = clone($this); // $this is owned by router instance, clone a new one for request instance
        $bag->routeArgs = $routeArgs;

        return $bag->attachToRequest($request);
    }

    public function get(string $key)
    {
        return $this->routeArgs[$key] ?? null;
    }

    public function exists(string $key)
    {
        return isset($this->routeArgs[$key]);
    }
}
