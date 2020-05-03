<?php

declare(strict_types=1);

namespace Lit\Bolt\Tests;

use Lit\Bolt\Middlewares\RequestContext;
use Lit\Nimo\Middlewares\MiddlewarePipe;
use Lit\Nimo\Middlewares\NoopMiddleware;
use PHPUnit\Framework\TestCase;

class RequestContextTest extends TestCase
{
    public function testAppend()
    {
        $rc = new RequestContext();
        $rc->append("val");
        self::assertEquals(["val"], $rc->getArrayCopy());

        $noop = new NoopMiddleware();
        $pipe = $rc->appendMiddleware($noop);
        self::assertInstanceOf(MiddlewarePipe::class, $pipe);
    }
}
