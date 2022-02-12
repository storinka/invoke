<?php

namespace Invoke\Meta;

use Invoke\Toolkit\Validators\ArrayOf;

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
