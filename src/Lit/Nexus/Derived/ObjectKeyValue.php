<?php

declare(strict_types=1);

namespace Lit\Nexus\Derived;

use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Traits\KeyValueTrait;

/**
 * KV object of an object content
 */
class ObjectKeyValue implements KeyValueInterface
{
    use KeyValueTrait;

    /**
     * @var object
     */
    protected $content;

    /**
     * ObjectKeyValue constructor.
     * @param object $content
     */
    protected function __construct($content)
    {
        $this->content = $content;
    }


    /**
     * @param object $content The content to be wrapped.
     * @return static
     */
    public static function wrap($content)
    {
        if ($content instanceof static) {
            return $content;
        }

        if (is_object($content)) {
            return new static($content);
        }

        throw new \InvalidArgumentException();
    }

    public function set(string $key, $value)
    {
        $this->content->{$key} = $value;
    }

    public function delete(string $key)
    {
        unset($this->content->{$key});
    }

    public function get(string $key)
    {
        return $this->content->{$key};
    }

    public function exists(string $key)
    {
        return isset($this->content->{$key});
    }

    public function getContent()
    {
        return $this->content;
    }
}
