<?php

declare(strict_types=1);

namespace Lit\Router\FastRoute\ArgumentHandler;

use Psr\Http\Message\ServerRequestInterface;

/**
 * @property ServerRequestInterface $request
 */
trait RouteArgumentBagGetterTrait
{
    public function routeArgs():RouteArgumentBag
    {
        return RouteArgumentBag::fromRequest($this->request);
    }
}
