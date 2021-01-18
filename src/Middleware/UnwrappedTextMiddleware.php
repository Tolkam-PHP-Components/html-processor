<?php declare(strict_types=1);

namespace Tolkam\HTMLProcessor\Middleware;

use Tolkam\DOM\Manipulator\Manipulator;
use Tolkam\HTMLProcessor\MiddlewareHandlerInterface;
use Tolkam\HTMLProcessor\MiddlewareInterface;

/**
 * Replaces root node's unwrapped text
 *
 * @package Tolkam\HTMLProcessor\Middleware
 */
class UnwrappedTextMiddleware implements MiddlewareInterface
{
    /**
     * @var string|null
     */
    protected ?string $wrapWith = null;
    
    /**
     * @var array
     */
    protected array $attributes = [];
    
    /**
     * @param string|null $wrapWith Tag name to wrap unwrapped text with or null to remove
     * @param array       $attributes
     */
    public function __construct(string $wrapWith = null, array $attributes = [])
    {
        $this->wrapWith = $wrapWith;
        $this->attributes = $attributes;
    }
    
    /**
     * @inheritDoc
     */
    public function apply(
        Manipulator $dom,
        MiddlewareHandlerInterface $middlewareHandler
    ): Manipulator {
        // replace in body or first root
        $xPathRoot = $dom->isHtmlDocument() ? '//body' : '//*[1]';
        
        if ($found = $dom->filterXPath($xPathRoot . '/text()[normalize-space()]')) {
            if ($this->wrapWith) {
                $wrapWith = strpos($this->wrapWith, '<') === false
                    ? '<' . $this->wrapWith . '>'
                    : $this->wrapWith;
                
                $wrap = $dom::create($wrapWith);
                foreach ($this->attributes as $name => $value) {
                    $wrap->setAttribute($name, $value);
                }
                $found->wrap($wrap);
            }
            else {
                $found->replaceWith('<!-- unwrapped-text-removed -->');
            }
        }
        
        return $middlewareHandler->handle($dom);
    }
}
