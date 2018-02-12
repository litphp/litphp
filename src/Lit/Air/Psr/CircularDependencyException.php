<?php

declare(strict_types=1);

namespace Lit\Air\Psr;

use Throwable;

class CircularDependencyException extends ContainerException
{
    /**
     * @var array
     */
    protected $stack;

    public function __construct(array $stack, Throwable $previous = null)
    {
        $this->stack = $stack;
        parent::__construct('circular dependency detected!', 0, $previous);
    }

    /**
     * @return array
     */
    public function getStack(): array
    {
        return $this->stack;
    }
}
