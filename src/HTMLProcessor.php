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
     * @param  $middleware
     *
     * @return self
     */
    public function addMiddleware($middleware): self
    {
        $this->middlewares->enqueue($middleware);
        
        return $this;
    }
    
    /**
     * @param string $html
     *
     * @return string
     */
    public function process(string $html): string
    {
        return $this->load($html)->outerHtml();
    }
    
    /**
     * Parses html string applying middlewares
     *
     * @param string $html
     *
     * @return Manipulator
     * @throws HTMLProcessorException
     */
    public function load(string $html): Manipulator
    {
        try {
            $dom = new Manipulator($html);
            if (!$dom->isHtmlDocument() && count($dom) > 1) {
                throw new HTMLProcessorException(
                    'HTML string must contain only one root element'
                );
            }
            
            $dom = $this->handle($dom);
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
    public function handle(Manipulator $dom): Manipulator
    {
        if ($this->middlewares->isEmpty()) {
            return $dom;
        }
        /** @var MiddlewareInterface $next */
        $next = $this->middlewares->dequeue();
        
        return $next->apply($dom, $this);
    }
}
