<?php

declare(strict_types=1);

namespace Lit\Bolt\Middlewares;

use Lit\Bolt\BoltEvent;
use Lit\Nimo\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventHookedMiddleware extends AbstractMiddleware
{
    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function main(): ResponseInterface
    {
        $this->attachToRequest();

        $beforeEvent = BoltEvent::of($this, [
            'request' => $this->request,
        ]);
        $this->eventDispatcher->dispatch(BoltEvent::EVENT_BEFORE_LOGIC, $beforeEvent);

        $interceptedResponse = $beforeEvent->getResponse();
        if ($interceptedResponse) {
            return $interceptedResponse;
        }

        $response = $this->delegate();

        $afterEvent = BoltEvent::of($this, [
            'request' => $this->request,
            'response' => $response,
        ]);
        $this->eventDispatcher->dispatch(BoltEvent::EVENT_AFTER_LOGIC, $afterEvent);

        return $afterEvent->getResponse();
    }
}
