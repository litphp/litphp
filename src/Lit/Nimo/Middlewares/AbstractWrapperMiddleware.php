<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Psr\Http\Server\MiddlewareInterface;
use Lit\Nimo\AbstractMiddleware;

/**
 * User: mcfog
 * Date: 15/9/4
 */
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
