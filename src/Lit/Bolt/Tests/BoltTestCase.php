<?php

declare(strict_types=1);

namespace Lit\Bolt\Tests;

use Lit\Bolt\BoltContainer;
use Lit\Nimo\Tests\NimoTestCase;

abstract class BoltTestCase extends NimoTestCase
{
    /**
     * @var BoltContainer
     */
    protected $container;

    protected function setUp()
    {
        parent::setUp();
        $this->container = new BoltContainer();
    }
}
