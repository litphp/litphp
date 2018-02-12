<?php

declare(strict_types=1);

namespace Nimo\Middlewares;

use Interop\Http\Server\MiddlewareInterface;
use Nimo\AbstractMiddleware;

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
