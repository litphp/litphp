<?php

declare(strict_types=1);

namespace Lit\Nimo\Middlewares;

use Lit\Nimo\Interfaces\ExceptionHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

/**
 * Middleware wrapper that try to catch soem exception when running inner middleware.
 */
class CatchMiddleware extends AbstractWrapperMiddleware
{
    /**
     * @var string
     */
    protected $catchClass;
    /**
     * @var ExceptionHandlerInterface
     */
    protected $catcher;

    public function __construct(
        $innerMiddleware,
        ExceptionHandlerInterface $catcher,
        string $catchClass = \Throwable::class
    ) {
        parent::__construct($innerMiddleware);
        $this->catchClass = $catchClass;
        $this->catcher = $catcher;
    }

    public static function catcher(
        ExceptionHandlerInterface $catcher,
        string $catchClass = \Throwable::class
    ): MiddlewareInterface {
        return NoopMiddleware::instance()->catch($catcher, $catchClass);
    }

    protected function main(): ResponseInterface
    {
        try {
            return $this->innerMiddleware->process($this->request, $this->handler);
        } catch (\Throwable $e) {
            if ($e instanceof $this->catchClass) {
                return $this->catcher->handle($e, $this->request, $this->handler, $this->innerMiddleware);
            }
            throw $e;
        }
    }
}
