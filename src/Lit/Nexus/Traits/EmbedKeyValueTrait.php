<?php namespace Lit\Nexus\Traits;

use Lit\Nexus\Interfaces\KeyValueInterface;

trait EmbedKeyValueTrait
{
    /**
     * @var KeyValueInterface
     */
    protected $innerKeyValue;

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->innerKeyValue->set($key, $value);
    }

    /**
     * @param string $key
     * @return void
     */
    public function delete($key)
    {
        $this->innerKeyValue->delete($key);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->innerKeyValue->get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->innerKeyValue->exists($key);
    }

    /**
     * @return KeyValueInterface
     */
    public function getInnerKeyValue()
    {
        return $this->innerKeyValue;
    }
}
