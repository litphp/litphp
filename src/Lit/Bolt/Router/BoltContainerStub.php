<?php

declare(strict_types=1);

namespace Lit\Bolt\Router;

use Lit\Air\ContainerStub;

/**
 * Helper class for resolving stub with container
 *
 * @deprecated moved to Lit\Bolt\ContainerStub
 */
class BoltContainerStub extends ContainerStub
{
    public function __construct(string $className, array $extraParameters = [])
    {
        trigger_error('\Lit\Bolt\Router\BoltContainerStub is moved to \Lit\Air\ContainerStub', E_USER_DEPRECATED);

        parent::__construct($className, $extraParameters);
    }
}
