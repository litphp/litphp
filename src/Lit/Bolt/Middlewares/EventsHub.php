<?php

declare(strict_types=1);

namespace Lit\Bolt\Middlewares;

use Lit\Bolt\BoltEvent;
use Lit\Nimo\Middlewares\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventsHub extends AbstractMiddleware
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher(): EventDispatcher
    {
        return $this->eventDispatcher;
    }

    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->eventDispatcher->addListener($eventName, $listener, $priority);
    }

    public function dispatch($eventName, Event $event = null)
    {
        $this->eventDispatcher->dispatch($eventName, $event);
    }

    protected function main(): ResponseInterface
    {
        $this->attachToRequest();

        $beforeEvent = BoltEvent::of($this, [
            'request' => $this->request,
        ]);
        $this->eventDispatcher->dispatch(BoltEvent::EVENT_BEFORE_LOGIC, $beforeEvent);

        $this->request = $beforeEvent->getRequest() ?: $this->request;
        $interceptedResponse = $beforeEvent->getResponse();
        if ($interceptedResponse) {
            $response = $interceptedResponse;
        } else {
            $response = $this->delegate();
        }

        $afterEvent = BoltEvent::of($this, [
            'request' => $this->request,
            'response' => $response,
        ]);
        $this->eventDispatcher->dispatch(BoltEvent::EVENT_AFTER_LOGIC, $afterEvent);

        return $afterEvent->getResponse() ?: $response;
    }
}
