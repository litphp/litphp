<?php namespace Lit\Bolt;

use Lit\Core\App;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class BoltAppEvent extends GenericEvent
{
    public static function of(App $app, array $arguments)
    {
        return new static($app, $arguments);
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
