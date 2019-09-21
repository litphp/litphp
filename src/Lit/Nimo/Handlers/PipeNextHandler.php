<?php

declare(strict_types=1);

namespace Lit\Nimo\Handlers;

use Lit\Nimo\Middlewares\MiddlewarePipe;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * State object as a RequestHandler, representing a "$next" handler.
 * Used with MiddlewarePipe.
 */
class PipeNextHandler implements RequestHandlerInterface
{
    /**
     * @var MiddlewarePipe
     */
    protected $pipe;
    /**
     * @var int
     */
    protected $index;

    /**
     * PipeNextHandler constructor.
     * @param MiddlewarePipe $pipe The MiddlewarePipe.
     * @param int $index Current iteration index.
     */
    public function __construct(MiddlewarePipe $pipe, int $index)
    {
        $this->pipe = $pipe;
        $this->index = $index;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->pipe->iterate($request, $this->index);
    }
}
