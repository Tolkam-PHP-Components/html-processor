<?php declare(strict_types=1);

namespace Tolkam\HTMLProcessor;

/**
 * Creates new instances of HTMLProcessor
 *
 * @package Tolkam\HTMLProcessor
 */
final class HTMLProcessorBuilder
{
    /**
     * @var array
     */
    private array $options = [];
    
    /**
     * @var array
     */
    private array $middlewares = [];
    
    /**
     * Sets the options
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options): self
    {
        if (empty($this->options)) {
            $this->options = $options;
        }
        
        return $this;
    }
    
    /**
     * Sets the middlewares
     *
     * @param MiddlewareInterface[] $middlewares
     *
     * @return self
     */
    public function setMiddlewares(MiddlewareInterface  ...$middlewares): self
    {
        if (empty($this->middlewares)) {
            $this->middlewares = $middlewares;
        }
        
        return $this;
    }
    
    /**
     * Gets the options
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
    
    /**
     * Gets the middlewares
     *
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
    
    /**
     * @return HTMLProcessor
     * @throws HTMLProcessorException
     */
    public function build(): HTMLProcessor
    {
        $instance = new HTMLProcessor($this->options);
        
        foreach ($this->middlewares as $middleware) {
            $instance->addMiddleware($middleware);
        }
        
        return $instance;
    }
}
