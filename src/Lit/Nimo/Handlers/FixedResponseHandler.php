<?php

declare(strict_types=1);

namespace Lit\Nimo\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Handler that always return fixed response.
 */
class FixedResponseHandler extends AbstractHandler
{
    protected $fixedResponse;

    /**
     * @param ResponseInterface $response The fixed response.
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
