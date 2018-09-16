<?php

declare(strict_types=1);

namespace Lit\Bolt\Tests;

use Lit\Air\Psr\Container;
use Lit\Bolt\BoltContainerConfiguration;
use Lit\Bolt\Zend\BoltZendConfiguration;
use Lit\Nimo\Tests\NimoTestCase;

abstract class BoltTestCase extends NimoTestCase
{
    /**
     * @var Container
     */
    protected $container;

    protected function setUp()
    {
        parent::setUp();
        $this->container = BoltContainerConfiguration::createContainer(BoltZendConfiguration::default());
    }
}
