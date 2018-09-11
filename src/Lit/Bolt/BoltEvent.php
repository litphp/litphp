<?php

declare(strict_types=1);

namespace Lit\Bolt;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class BoltEvent
 * @package Lit\Bolt
 *
 * @method MiddlewareInterface getSubject()
 */
class BoltEvent extends GenericEvent
{
    const EVENT_BEFORE_LOGIC = 'bolt.before_logic';
    const EVENT_AFTER_LOGIC = 'bolt.after_logic';

    public static function of(MiddlewareInterface $middleware, array $arguments = [])
    {
        return new static($middleware, $arguments);
    }

    public function getResponse(): ?ResponseInterface
    {
        return $this['response'] ?? null;
    }

    public function getRequest(): ?RequestInterface
    {
        return $this['request'] ?? null;
    }
}
