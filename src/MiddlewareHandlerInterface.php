<?php declare(strict_types=1);

namespace Tolkam\HTMLProcessor;

use Tolkam\DOM\Manipulator\Manipulator;

interface MiddlewareHandlerInterface
{
    /**
     * @param Manipulator $dom
     *
     * @return Manipulator
     */
    public function handle(Manipulator $dom): Manipulator;
}
