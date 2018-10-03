<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Injection\SetterInjector;
use Lit\Bolt\Middlewares\RequestContext;
use Lit\Bolt\Middlewares\EventsHub;
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

    protected function events(): EventsHub
    {
        return EventsHub::fromRequest($this->request);
    }

    protected function context(): RequestContext
    {
        return RequestContext::fromRequest($this->request);
    }
}
