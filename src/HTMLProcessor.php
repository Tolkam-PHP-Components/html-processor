<?php declare(strict_types=1);

namespace Tolkam\HTMLProcessor;

use SplQueue;
use Throwable;
use Tolkam\DOM\Manipulator\Manipulator;

class HTMLProcessor implements MiddlewareHandlerInterface
{
    /**
     * @var SplQueue
     */
    private SplQueue $middlewares;
    
    /**
     * @var array
     */
    private array $options = [
        'throwOnError' => false,
        'outputOnError' => '<!-- HTML processor error -->',
    ];
    
    /**
     * @param array $options
     *
     * @throws HTMLProcessorException
     */
    public function __construct(array $options = [])
    {
        if ($unknown = array_diff(array_keys($options), array_keys($this->options))) {
            throw new HTMLProcessorException(sprintf(
                'Unknown options: "%s"',
                implode('", "', $unknown)
            ));
        }
        
        $this->options = array_replace($this->options, $options);
        $this->middlewares = new SplQueue;
    }
    
    /**
     * Adds middleware
     *
     * @param MiddlewareInterface $middleware
     *
     * @return self
     */
    public function addMiddleware(MiddlewareInterface $middleware): self
    {
        $this->middlewares->enqueue($middleware);
        
        return $this;
    }
    
    /**
     * @param string     $html
     * @param mixed|null $context - Arbitrary context to pass to middlewares
     *
     * @return string
     * @throws HTMLProcessorException
     */
    public function process(string $html, $context = null): string
    {
        return $this->load($html, $context)->outerHtml();
    }
    
    /**
     * Parses html string applying middlewares
     *
     * @param string $html
     * @param null   $context - Arbitrary context to pass to middlewares
     *
     * @return Manipulator
     */
    public function load(string $html, $context = null): Manipulator
    {
        try {
            $dom = new Manipulator($html);
            if (!$dom->isHtmlDocument() && count($dom) > 1) {
                throw new HTMLProcessorException(
                    'HTML string must contain only one root element'
                );
            }
            
            // copy middlewares
            $middlewares = clone $this->middlewares;
            $dom = $this->handle($dom, $context ?? new Context);
            
            // set middlewares again so multiple calls of `load()` are possible
            // with the same middlewares
            $this->middlewares = $middlewares;
        } catch (Throwable $t) {
            if (!!$this->options['throwOnError']) {
                throw $t;
            }
            
            error_log((string) $t);
            $dom = Manipulator::create(
                '<div>' . $this->options['outputOnError'] . '</div>'
            );
        }
        
        return $dom;
    }
    
    /**
     * @inheritDoc
     */
    public function handle(Manipulator $dom, Context $context): Manipulator
    {
        if ($this->middlewares->isEmpty()) {
            return $dom;
        }
        /** @var MiddlewareInterface $next */
        $next = $this->middlewares->dequeue();
        
        return $next->apply($dom, $this, $context);
    }
}
