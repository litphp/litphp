<?php

declare(strict_types=1);

namespace Lit\Nimo\Tests;

trait RememberConstructorParamTrait
{
    protected $params;
    public function __construct()
    {
        $this->params = func_get_args();
    }
}
