<?php

namespace Invoke;

/**
 * Type pipe.
 *
 * @template IType
 * @template RType
 *
 * @extends Pipe<IType, RType>
 */
interface Type extends Pipe
{
    /**
     * Unique type name.
     *
     * @return string
     */
    public static function invoke_getTypeName(): string;
}
