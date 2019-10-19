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

/**
 * Bolt application
 */
class BoltApp extends App
{
    public const SETTER_INJECTOR = SetterInjector::class;
    /**
     * @var ?EventsHub
     * @deprecated
     */
    protected $eventsHub;
    /**
     * @var ?RequestContext
     */
    protected $requestContext;

    /**
     * Injector for EventsHub
     *
     * @param EventsHub|null $eventsHub The EventsHub.
     * @return $this
     * @deprecated
     */
    public function injectEventsHub(?EventsHub $eventsHub)
    {
        @trigger_error('EventsHub is deprecated', E_USER_DEPRECATED);
        $this->eventsHub = $eventsHub;
        return $this;
    }

    /**
     * Injector for RequestContext
     *
     * @param RequestContext|null $requestContext The RequestContext.
     * @return $this
     */
    public function injectRequestContext(?RequestContext $requestContext)
    {
        $this->requestContext = $requestContext;
        return $this;
    }

    /**
     * Getter for EventsHub
     *
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
