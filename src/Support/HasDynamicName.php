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
    public function getDynamicName(): string;
}