<?php

declare(strict_types=1);

namespace Lit\Bolt\Tests;

use Lit\Air\Psr\Container;
use Lit\Bolt\BoltContainerConfiguration;
use Lit\Nimo\Tests\NimoTestCase;
use Lit\Runner\LaminasSapi\BoltLaminasConfiguration;

abstract class BoltTestCase extends NimoTestCase
{
    /**
     * @var Container
     */
    protected $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = BoltContainerConfiguration::createContainer(BoltLaminasConfiguration::default());
    }
}
