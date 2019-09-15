<?php

declare(strict_types=1);

namespace Lit\Air\Injection;

use Lit\Air\Factory;

/**
 * InjectorInterface represents a injector, which is a policy of DI injection other than constructor injection.
 */
interface InjectorInterface
{
    /**
     * Decide whether provided object is target of this injector.
     *
     * @param object $obj The object to be checked.
     * @return boolean
     */
    public function isTarget($obj): bool;

    /**
     * Do the injection process.
     *
     * @param Factory $factory The factory.
     * @param object  $obj     The Object.
     * @param array   $extra   Extra parameters.
     * @return void
     */
    public function inject(Factory $factory, $obj, array $extra = []): void;
}
