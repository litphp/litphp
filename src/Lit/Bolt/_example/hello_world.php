<?php

// @codeCoverageIgnoreStart
// phpcs:disable PSR1.Files.SideEffects
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Lit\Air\Configurator as C;
use Lit\Bolt\BoltAbstractAction;
use Lit\Bolt\BoltApp;
use Lit\Runner\ZendSapi\BoltZendRunner;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

is_readable(__DIR__ . '/../vendor/autoload.php')
    ? require_once __DIR__ . '/../vendor/autoload.php'
    : require_once __DIR__ . '/../../../../vendor/autoload.php';

class HelloAction extends BoltAbstractAction
{
    protected function main(): ResponseInterface
    {
        return $this->json()->render([
            'hello' => 'world',
            'method' => $this->request->getMethod(),
            'uri' => $this->request->getUri()->__toString(),
        ]);
    }
}

BoltZendRunner::run([
    BoltApp::class => C::produce(BoltApp::class, [
        RequestHandlerInterface::class => C::produce(HelloAction::class),
    ]),
]);

// @codeCoverageIgnoreEnd
