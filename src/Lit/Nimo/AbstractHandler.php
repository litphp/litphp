<?php

declare(strict_types=1);

namespace Nimo;

use Interop\Http\Server\RequestHandlerInterface;
use Nimo\Traits\HandlerTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractHandler implements RequestHandlerInterface
{
    use HandlerTrait;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;
        return $this->main();
    }

    /**
     * @return ResponseInterface
     */
    abstract protected function main(): ResponseInterface;
}
