<?php

namespace Invoke;

/**
 * Indicates that pipeline should be stopped.
 */
class Stop
{
    public function __construct(public mixed $value)
    {
    }
}