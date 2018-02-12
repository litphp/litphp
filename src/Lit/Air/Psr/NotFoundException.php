<?php

declare(strict_types=1);

namespace Lit\Air\Psr;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{

}
