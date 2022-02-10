<?php

namespace Invoke\Support;

use Invoke\Validators\ArrayOf;

/**
 * Says that type name is generated at runtime.
 *
 * @see ArrayOf
 */
interface HasDynamicName
{
    /**
     * Dynamically generated type name.
     *
     * @return string
     */
    public function invoke_getDynamicName(): string;
}