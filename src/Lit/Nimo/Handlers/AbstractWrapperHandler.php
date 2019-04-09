<?php

declare(strict_types=1);

namespace Lit\Nimo\Handlers;

use Lit\Nimo\AbstractHandler;
use Psr\Http\Server\RequestHandlerInterface;

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
