<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Lit\Air\Injection\SetterInjector;
use Lit\Bolt\Middlewares\EventsHub;
use Lit\Bolt\Middlewares\RequestContext;
use Lit\Nimo\Middlewares\MiddlewarePipe;
use Lit\Voltage\App;
use Lit\Voltage\Interfaces\ThrowableResponseInterface;
use Psr\Http\Message\ResponseInterface;

class BoltApp extends App
{
    const SETTER_INJECTOR = SetterInjector::class;
    /**
     * @var ?EventsHub
     */
    protected $eventsHub;
    /**
     * @var ?RequestContext
     */
    protected $requestContext;

    public function injectEventsHub(?EventsHub $eventsHub)
    {
        $this->eventsHub = $eventsHub;
        return $this;
    }

    public function injectRequestContext(?RequestContext $requestContext)
    {
        $this->requestContext = $requestContext;
        return $this;
    }

    /**
     * @return EventsHub
     */
    public function getEventsHub(): EventsHub
    {
        assert($this->eventsHub !== null);
        return $this->eventsHub;
    }

    protected function main(): ResponseInterface
    {
        try {
            $pipe = new MiddlewarePipe();
            if (isset($this->requestContext)) {
                $pipe->append($this->requestContext);
            }
            if (isset($this->eventsHub)) {
                $pipe->append($this->eventsHub);
            }
            $pipe->append($this->middlewarePipe);

            return $pipe->process($this->request, $this->businessLogicHandler);
        } catch (ThrowableResponseInterface $e) {
            return $e->getResponse();
        }
    }
}
