<?php

declare(strict_types=1);

namespace Lit\Bolt\Router;

use Lit\Core\Interfaces\ThrowableResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class StubResolveException extends \RuntimeException implements ThrowableResponseInterface
{
    protected $stub;
    /**
     * @var ResponseFactoryInterface
     */
    protected $factory;

    public function __construct(
        $stub,
        ResponseFactoryInterface $factory,
        Throwable $previous = null,
        string $message = 'router stub parse failed',
        int $code = 0
    ) {
        parent::__construct($message, $code, $previous);
        $this->stub = $stub;
        $this->factory = $factory;
    }

    /**
     * @return mixed
     */
    public function getStub()
    {
        return $this->stub;
    }

    public function getResponse(): ResponseInterface
    {
        $response = $this->factory->createResponse(404);
        $response->getBody()->write('not found');

        return $response;
    }
}
