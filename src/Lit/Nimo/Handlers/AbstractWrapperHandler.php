<?php

declare(strict_types=1);

namespace Lit\Nimo\Handlers;

use Psr\Http\Server\RequestHandlerInterface;

/**
 * Base class for handler wrapper.
 */
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
