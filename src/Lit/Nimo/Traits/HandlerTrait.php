<?php

declare(strict_types=1);

namespace Lit\Nimo\Traits;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait HandlerTrait
{
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
