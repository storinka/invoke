<?php

namespace Invoke\Support;

/**
 * Says that object can be converted to array using "toArray" method.
 */
interface HasToArray
{
    /**
     * Convert object to array.
     *
     * @return array
     */
    public function toArray(): array;
}