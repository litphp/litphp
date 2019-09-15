<?php

declare(strict_types=1);

namespace Lit\Nexus\Derived;

use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Traits\KeyValueTrait;

/**
 * KV object of an offset content (array or ArrayAccess)
 */
class OffsetKeyValue implements KeyValueInterface
{
    use KeyValueTrait;

    protected $content;

    protected function __construct($content)
    {
        if (!($content instanceof \ArrayAccess) && !is_array($content)) {
            throw new \InvalidArgumentException();
        }

        $this->content = $content;
    }

    /**
     * @param array|\ArrayAccess $content The content to be wrapped.
     * @return static
     */
    public static function wrap($content)
    {
        if ($content instanceof static) {
            return $content;
        }

        return new static($content);
    }

    public function set(string $key, $value)
    {
        $this->content[$key] = $value;
    }

    public function delete(string $key)
    {
        unset($this->content[$key]);
    }

    public function get(string $key)
    {
        return $this->content[$key];
    }

    public function exists(string $key)
    {
        return isset($this->content[$key]);
    }

    /**
     * @return array|\ArrayAccess
     */
    public function getContent()
    {
        return $this->content;
    }
}
