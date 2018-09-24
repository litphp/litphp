<?php

declare(strict_types=1);

namespace Lit\Bolt\Zend;

use Http\Factory\Diactoros\ResponseFactory;
use Lit\Air\Configurator as C;
use Lit\Bolt\BoltApp;
use Psr\Http\Message\ResponseFactoryInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;
use Zend\HttpHandlerRunner\RequestHandlerRunner;

class BoltZendConfiguration
{
    public static function default()
    {
        return [
            RequestHandlerRunner::class => C::provideParameter([
                C::alias(BoltApp::class),
                C::produce(SapiEmitter::class),
                C::value([ServerRequestFactory::class, 'fromGlobals']),
                C::value(function (\Throwable $e) {
                    throw $e;
                }),
            ]),
            ResponseFactoryInterface::class => C::produce(ResponseFactory::class),
        ];
    }
}
