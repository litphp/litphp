<?php

declare(strict_types=1);

namespace Lit\Runner\ZendSapi;

use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Lit\Air\Configurator as C;
use Lit\Bolt\BoltApp;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Configuration for zend runner
 */
class BoltZendConfiguration
{
    public static function default()
    {
        return [
            RequestHandlerRunner::class => C::produce(RequestHandlerRunner::class, [
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
