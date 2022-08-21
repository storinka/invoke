<?php

namespace Invoke;

/**
 * Basic component of Invoke. Works as a filter.
 *
 * @template IType
 * @template RType
 */
interface Pipe
{
    /**
     * @param IType $value
     * @return RType
     */
    public function run(mixed $value): mixed;
}
