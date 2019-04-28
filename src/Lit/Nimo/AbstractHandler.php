<?php

declare(strict_types=1);

namespace Lit\Nimo;

use Lit\Nimo\Traits\HandlerCompositeTrait;
use Lit\Nimo\Traits\HandlerTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractHandler implements RequestHandlerInterface
{
    use HandlerTrait;
    use HandlerCompositeTrait;
}
