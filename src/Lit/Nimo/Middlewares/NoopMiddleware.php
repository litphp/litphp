<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Psr\Http\Message\ResponseInterface;

final class NoopMiddleware extends AbstractMiddleware
{
    public static function instance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new self();
        }
        return $instance;
    }

    protected function main(): ResponseInterface
    {
        return $this->delegate();
    }
}
