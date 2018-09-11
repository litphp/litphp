<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Lit\Nimo\AbstractMiddleware;
use Psr\Http\Server\MiddlewareInterface;

abstract class AbstractWrapperMiddleware extends AbstractMiddleware
{
    /**
     * @var MiddlewareInterface
     */
    protected $innerMiddleware;

    public function __construct(MiddlewareInterface $innerMiddleware)
    {
        $this->innerMiddleware = $innerMiddleware;
    }
}
