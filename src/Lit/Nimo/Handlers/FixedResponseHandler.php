<?php

declare(strict_types=1);

namespace Lit\Nimo\Handlers;

use Psr\Http\Server\RequestHandlerInterface;
use Lit\Nimo\AbstractHandler;
use Psr\Http\Message\ResponseInterface;

class FixedResponseHandler extends AbstractHandler
{
    protected $fixedResponse;

    /**
     * @param ResponseInterface $response always return this response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->fixedResponse = $response;
    }

    public static function wrap(ResponseInterface $response): RequestHandlerInterface
    {
        return new static($response);
    }

    protected function main(): ResponseInterface
    {
        return $this->fixedResponse;
    }
}
