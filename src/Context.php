<?php declare(strict_types=1);

namespace Tolkam\HTMLProcessor;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

class Context implements ArrayAccess, IteratorAggregate
{
    /**
     * @var array
     */
    private array $data = [];
    
    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $k => $v) {
            $this->set($k, $v);
        }
    }
    
    /**
     * @param string $key
     * @param        $value
     *
     * @return $this
     */
    public function set(string $key, $value): self
    {
        $this->data[$key] = $value;
        
        return $this;
    }
    
    /**
     * @param string $key
     * @param        $value
     */
    public function add(string $key, $value)
    {
        if (isset($this->data[$key]) && array_key_exists($key, $value)) {
            if (is_array($this->data[$key])) {
                $this->data[$key][] = $value;
            }
        }
        else {
            $this->set($key, $value);
        }
    }
    
    /**
     * @param string|null $key
     * @param null        $default
     *
     * @return mixed|null
     */
    public function get(string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->data;
        }
        
        return $this->data[$key] ?? $default;
    }
    
    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }
    
    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }
    
    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }
    
    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }
    
    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
