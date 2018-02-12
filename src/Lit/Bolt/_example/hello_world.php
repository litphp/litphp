<?php

use Lit\Bolt\BoltAction;
use Lit\Bolt\BoltApp;
use Lit\Bolt\BoltContainer;
use Psr\Http\Message\ResponseInterface;

require(__DIR__ . '/../vendor/autoload.php');

class HelloAction extends BoltAction
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

BoltApp::run(new BoltContainer([
    BoltApp::MAIN_HANDLER => ['$' => 'autowire', HelloAction::class]
]));
