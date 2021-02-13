<?php declare(strict_types=1);

namespace Tolkam\HTMLProcessor;

use ArrayAccess;
use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use RuntimeException;
use Throwable;
use TypeError;

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
     * @param string|null   $key
     * @param null          $default
     * @param callable|null $validator
     *
     * @return mixed|null
     */
    public function get(string $key = null, $default = null, callable $validator = null)
    {
        if ($key === null) {
            return $this->data;
        }
        
        return $this->validateValue($key, $this->data[$key], $validator) ?? $default;
    }
    
    /**
     * @param string        $key
     * @param callable|null $validator
     *
     * @return mixed
     */
    public function getRequired(string $key, callable $validator = null)
    {
        if (!isset($this->data[$key]) && !array_key_exists($key, $this->data)) {
            throw new InvalidArgumentException(sprintf(
                'Required context value for "%s" is not set',
                $key
            ));
        }
        
        return $this->validateValue($key, $this->data[$key], $validator);
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
    
    /**
     * @param string        $key
     * @param               $value
     * @param callable|null $validator
     *
     * @return mixed
     */
    private function validateValue(string $key, $value, callable $validator = null)
    {
        if (!$validator) {
            return $value;
        }
        
        $message = sprintf('Value for "%s" did not pass supplied validator', $key);
        
        try {
            $result = $validator($value);
            
            if ($result === false) {
                throw new TypeError($message);
            }
            
            return $value;
        } catch (Throwable $t) {
            $thrownMessage = $t->getMessage();
            if ($thrownMessage !== $message) {
                $message .= ' (' . $thrownMessage . ')';
            }
            
            throw new RuntimeException(sprintf(
                $message,
                $key
            ));
        }
    }
}
