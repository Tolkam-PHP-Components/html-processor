# tolkam/html-processor

Processes HTML applying custom transformations.

## Documentation

The code is rather self-explanatory and API is intended to be as simple as possible. Please, read the sources/Docblock if you have any questions. See [Usage](#usage) for quick start.

## Usage

````php
use Tolkam\DOM\Manipulator\Manipulator;
use Tolkam\HTMLProcessor\Context;
use Tolkam\HTMLProcessor\HTMLProcessor;
use Tolkam\HTMLProcessor\MiddlewareHandlerInterface;
use Tolkam\HTMLProcessor\MiddlewareInterface;

$processor = new HTMLProcessor;

$processor->addMiddleware(new class implements MiddlewareInterface {
    public function apply(
        Manipulator $dom,
        MiddlewareHandlerInterface $middlewareHandler,
        Context $context
    ): Manipulator {
        // convert each <div> contents to uppercase
        $dom->filter('div')->each(function(Manipulator $element) {
            $element->setInnerHtml(strtoupper($element->getCombinedText()));
        });
        
        return $middlewareHandler->handle($dom, $context);
    }
});

$processor->addMiddleware(new class implements MiddlewareInterface {
    public function apply(
        Manipulator $dom,
        MiddlewareHandlerInterface $middlewareHandler,
        Context $context
    ): Manipulator {
        // wrap each <div> contents with <b> tag
        $dom->filter('div')->each(function(Manipulator $element) {
            $element->wrapInner('<b>');
        });
        
        return $middlewareHandler->handle($dom, $context);
    }
});

echo $processor->process('<div>My text</div>');
````

## License

Proprietary / Unlicensed 🤷
