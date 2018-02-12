<?php

declare(strict_types=1);

namespace Lit\Air\Psr;

use Psr\Container\ContainerExceptionInterface;

class ContainerException extends \LogicException implements ContainerExceptionInterface
{

}
