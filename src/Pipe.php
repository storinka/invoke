<?php

namespace Invoke;

/**
 * Basic component of Invoke.
 */
interface Pipe
{
    public function pass(mixed $value): mixed;
}
