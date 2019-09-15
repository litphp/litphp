<?php

declare(strict_types=1);

namespace Lit\Nimo\Handlers;

use Lit\Nimo\Traits\HandlerCompositeTrait;
use Lit\Nimo\Traits\HandlerTrait;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Base class for RequestHandler
 */
abstract class AbstractHandler implements RequestHandlerInterface
{
    use HandlerTrait;
    use HandlerCompositeTrait;
}
