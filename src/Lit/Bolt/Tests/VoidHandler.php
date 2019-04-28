<?php

declare(strict_types=1);

namespace Lit\Bolt\Tests;

use Lit\Nimo\Handlers\AbstractHandler;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\EmptyResponse;

class VoidHandler extends AbstractHandler
{
    protected function main(): ResponseInterface
    {
        return new EmptyResponse();
    }
}
