<?php

declare(strict_types=1);

namespace Lit\Nimo\Handlers;

use Interop\Http\Server\RequestHandlerInterface;
use Lit\Nimo\AbstractHandler;

abstract class AbstractWrapperHandler extends AbstractHandler
{
    /**
     * @var RequestHandlerInterface
     */
    protected $innerHandler;

    public function __construct(RequestHandlerInterface $handler)
    {
        $this->innerHandler = $handler;
    }
}
