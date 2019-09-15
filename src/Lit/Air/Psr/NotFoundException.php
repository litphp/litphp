<?php

declare(strict_types=1);

namespace Lit\Air\Psr;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Concrete NotFoundExceptionInterface
 */
class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{

}
