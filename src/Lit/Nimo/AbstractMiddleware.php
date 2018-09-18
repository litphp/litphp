<?php

declare(strict_types=1);

namespace Lit\Nimo;

use Lit\Nimo\Traits\AttachToRequestTrait;
use Lit\Nimo\Traits\MiddlewareCompositeTrait;
use Lit\Nimo\Traits\MiddlewareTrait;
use Psr\Http\Server\MiddlewareInterface;

abstract class AbstractMiddleware implements MiddlewareInterface
{
    use MiddlewareTrait;
    use AttachToRequestTrait;
    use MiddlewareCompositeTrait;
}
