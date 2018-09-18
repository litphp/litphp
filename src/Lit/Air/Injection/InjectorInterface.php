<?php

declare(strict_types=1);

namespace Lit\Air\Injection;

use Lit\Air\Factory;

interface InjectorInterface
{
    public function isTarget($obj): bool;

    public function inject(Factory $factory, $obj, array $extra = []): void;
}
