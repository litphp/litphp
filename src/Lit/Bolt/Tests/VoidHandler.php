<?php

declare(strict_types=1);

namespace Lit\Bolt\Tests;

use Laminas\Diactoros\Response\EmptyResponse;
use Lit\Nimo\Handlers\AbstractHandler;
use Psr\Http\Message\ResponseInterface;

class VoidHandler extends AbstractHandler
{
    protected function main(): ResponseInterface
    {
        return new EmptyResponse();
    }
}
