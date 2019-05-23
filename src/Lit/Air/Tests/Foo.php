<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

use Lit\Air\Injection\SetterInjector;

class Foo
{
    const SETTER_INJECTOR = SetterInjector::class;

    public $bar;
    /**
     * @var \stdClass
     */
    public $baz;
    /**
     * @var int
     */
    public $qux;
    /**
     * @var string
     */
    public $ng;

    /**
     * @var \SplObjectStorage
     */
    private $splObjectStorage;

    public function injectSplObjectStorage(\SplObjectStorage $splObjectStorage)
    {
        $this->splObjectStorage = $splObjectStorage;
        return $this;
    }

    public function shoudNotBeInjected(\SplObjectStorage $splObjectStorage)
    {
        throw new \Exception('method must prefixed "inject" to be injected');
    }

    public function __construct(
        $bar,
        \stdClass $baz,
        int $qux = 42,
        string $ng = null
    ) {
        $this->bar = $bar;
        $this->baz = $baz;
        $this->qux = $qux;
        $this->ng = $ng;
    }


    public static function identity($x)
    {
        return $x;
    }

    /**
     * @return \SplObjectStorage
     */
    public function getSplObjectStorage()
    {
        return $this->splObjectStorage;
    }
}
