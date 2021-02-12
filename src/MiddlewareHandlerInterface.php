<?php declare(strict_types=1);

namespace Tolkam\HTMLProcessor;

use Tolkam\DOM\Manipulator\Manipulator;

interface MiddlewareHandlerInterface
{
    /**
     * @param Manipulator $dom
     * @param Context     $context - Arbitrary context to pass to middlewares
     *
     * @return Manipulator
     */
    public function handle(Manipulator $dom, Context $context): Manipulator;
}
