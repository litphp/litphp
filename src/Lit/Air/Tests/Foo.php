<?php

declare(strict_types=1);

namespace Lit\Air\Tests;

class Foo
{
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
}
