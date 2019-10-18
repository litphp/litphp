<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Event class for Bolt
 *
 * @method MiddlewareInterface getSubject()
 */
class BoltEvent extends GenericEvent
{
    public const EVENT_BEFORE_LOGIC = 'bolt.before_logic';
    public const EVENT_AFTER_LOGIC = 'bolt.after_logic';

    /**
     * Create a new bolt event
     *
     * @param MiddlewareInterface $middleware The subject middleware.
     * @param array               $arguments  Event arguments.
     * @return BoltEvent
     */
    public static function of(MiddlewareInterface $middleware, array $arguments = [])
    {
        return new static($middleware, $arguments);
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this['response'] ?? null;
    }

    public function getRequest(): ?ServerRequestInterface
    {
        return $this['request'] ?? null;
    }
}
