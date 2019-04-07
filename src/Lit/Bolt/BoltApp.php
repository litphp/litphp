<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Injection\SetterInjector;
use Lit\Bolt\Middlewares\EventsHub;
use Lit\Bolt\Middlewares\RequestContext;
use Lit\Voltage\App;
use Lit\Voltage\Interfaces\ThrowableResponseInterface;
use Lit\Nimo\MiddlewarePipe;
use Psr\Http\Message\ResponseInterface;

class BoltApp extends App
{
    const SETTER_INJECTOR = SetterInjector::class;
    /**
     * @var EventsHub
     */
    protected $eventsHub;

    public function injectEventsHub(EventsHub $eventsHub)
    {
        $this->eventsHub = $eventsHub;
        return $this;
    }

    /**
     * @var RequestContext
     */
    protected $requestContext;

    public function injectRequestContext(RequestContext $requestContext)
    {
        $this->requestContext = $requestContext;
        return $this;
    }

    /**
     * @return EventsHub
     */
    public function getEventsHub(): EventsHub
    {
        return $this->eventsHub;
    }

    protected function main(): ResponseInterface
    {
        try {
            $pipe = (new MiddlewarePipe())
                ->append($this->requestContext)
                ->append($this->eventsHub)
                ->append($this->middlewarePipe);
            return $pipe->process($this->request, $this->businessLogicHandler);
        } catch (ThrowableResponseInterface $e) {
            return $e->getResponse();
        }
    }
}
