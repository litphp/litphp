<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Interop\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

class CatchMiddleware extends AbstractWrapperMiddleware
{
    /**
     * @var string
     */
    protected $catchClass;
    /**
     * @var callable
     */
    protected $catcher;

    public function __construct($innerMiddleware, callable $catcher, string $catchClass = \Throwable::class)
    {
        parent::__construct($innerMiddleware);
        $this->catchClass = $catchClass;
        $this->catcher = $catcher;
    }

    public static function catcher(callable $catcher, string $catchClass = \Throwable::class): MiddlewareInterface
    {
        return NoopMiddleware::instance()->catch($catcher, $catchClass);
    }

    protected function main(): ResponseInterface
    {
        try {
            return $this->innerMiddleware->process($this->request, $this->handler);
        } catch (\Throwable $e) {
            if ($e instanceof $this->catchClass) {
                return ($this->catcher)($e, $this->request, $this->handler);
            }
            throw $e;
        }
    }
}
