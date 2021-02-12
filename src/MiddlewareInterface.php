<?php declare(strict_types=1);

namespace Tolkam\HTMLProcessor;

use Tolkam\DOM\Manipulator\Manipulator;

interface MiddlewareInterface
{
    /**
     * Applies transformations or returns a Manipulator as final result
     *
     * @param Manipulator                $dom
     * @param MiddlewareHandlerInterface $middlewareHandler
     * @param Context|null               $context
     *
     * @return Manipulator
     */
    public function apply(
        Manipulator $dom,
        MiddlewareHandlerInterface $middlewareHandler,
        Context $context
    ): Manipulator;
}
