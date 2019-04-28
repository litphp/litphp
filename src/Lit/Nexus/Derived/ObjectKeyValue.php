<?php

declare(strict_types=1);

namespace Lit\Nexus\Derived;

use Lit\Nexus\Interfaces\KeyValueInterface;
use Lit\Nexus\Traits\KeyValueTrait;

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
     * @param object $content
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

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value)
    {
        $this->content->{$key} = $value;
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete(string $key)
    {
        unset($this->content->{$key});
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->content->{$key};
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists(string $key)
    {
        return isset($this->content->{$key});
    }

    /**
     * @return object
     */
    public function getContent()
    {
        return $this->content;
    }
}
