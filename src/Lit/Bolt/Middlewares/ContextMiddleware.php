<?php

declare(strict_types=1);

namespace Lit\Bolt\Middlewares;

use Lit\Nimo\Traits\AttachToRequestTrait;
use Lit\Nimo\Traits\MiddlewareCompositeTrait;
use Lit\Nimo\Traits\MiddlewareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class ContextMiddleware extends \ArrayObject implements MiddlewareInterface
{
    use MiddlewareTrait;
    use AttachToRequestTrait;
    use MiddlewareCompositeTrait;

    public function __construct(array $input = [])
    {
        parent::__construct($input, \ArrayObject::ARRAY_AS_PROPS);
    }

    protected function main(): ResponseInterface
    {
        $this->attachToRequest();
        return $this->delegate();
    }
}
