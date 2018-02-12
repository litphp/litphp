<?php

declare(strict_types=1);

namespace Nimo\Handlers;

use Interop\Http\Server\RequestHandlerInterface;
use Nimo\AbstractHandler;

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
