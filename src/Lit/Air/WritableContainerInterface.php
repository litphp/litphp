<?php

declare(strict_types=1);

namespace Lit\Air;

use Psr\Container\ContainerInterface;

interface WritableContainerInterface extends ContainerInterface
{
    /**
     * @inheritdoc
     */
    public function get($id);

    /**
     * @inheritdoc
     */
    public function has($id);

    /**
     * Set the value of $id
     * return $this
     *
     * @param $id
     * @param $value
     *
     * @return $this
     */
    public function set($id, $value);
}