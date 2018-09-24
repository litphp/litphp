<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Injection\SetterInjector;
use Lit\Bolt\Middlewares\ContextMiddleware;
use Lit\Bolt\Middlewares\EventMiddleware;
use Lit\Core\AbstractAction;
use Psr\Http\Message\ResponseFactoryInterface;

abstract class BoltAbstractAction extends AbstractAction
{
    const SETTER_INJECTOR = SetterInjector::class;

    /**
     *
     * @param ResponseFactoryInterface $responseFactory
     * @return $this
     */
    public function injectResponseFactory(ResponseFactoryInterface $responseFactory): BoltAbstractAction
    {
        $this->responseFactory = $responseFactory;

        return $this;
    }

    protected function events(): EventMiddleware
    {
        return EventMiddleware::fromRequest($this->request);
    }

    protected function context(): ContextMiddleware
    {
        return ContextMiddleware::fromRequest($this->request);
    }
}
