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
     * @param IType|Stop $value
     * @return RType|Stop
     */
    public function pass(mixed $value): mixed;
}
