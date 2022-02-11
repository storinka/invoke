<?php

namespace Invoke;

/**
 * Type pipe.
 */
interface Type extends Pipe
{
    /**
     * Unique type name.
     *
     * @return string
     */
    public static function invoke_getName(): string;
}
