<?php

declare(strict_types=1);

namespace Lit\Nexus\Traits;

trait RememberConstructorParamTrait
{
    protected $params;

    public function __construct(...$params)
    {
        $this->params = $params;
    }
}
