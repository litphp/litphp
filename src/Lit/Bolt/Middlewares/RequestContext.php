<?php

declare(strict_types=1);

namespace Lit\Bolt\Middlewares;

use Lit\Nimo\Traits\AttachToRequestTrait;
use Lit\Nimo\Traits\MiddlewareCompositeTrait;
use Lit\Nimo\Traits\MiddlewareTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Subclass of SPL ArrayObject that can attach to a PSR request as middleware.
 * Useful the store arbitary context without polluting request attributes.
 */
class RequestContext extends \ArrayObject implements MiddlewareInterface
{
    use MiddlewareTrait;
    use AttachToRequestTrait;
    use MiddlewareCompositeTrait {
        append as appendMiddleware; // avoid override ArrayObject::append
        prepend as prependMiddleware; // for consistency, also rename prepend
    }

    public function __construct(array $input = [])
    {
        parent::__construct($input, \ArrayObject::ARRAY_AS_PROPS);
    }

    public function append($value)
    {
        parent::append($value);
    }

    protected function main(): ResponseInterface
    {
        $this->attachToRequest();
        return $this->delegate();
    }
}
