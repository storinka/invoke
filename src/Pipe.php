<?php

namespace Invoke;

/**
 * Basic component of Invoke.
 */
interface Pipe
{
    /**
     * @param mixed|Stop $value
     * @return mixed|Stop
     */
    public function pass(mixed $value): mixed;
}
