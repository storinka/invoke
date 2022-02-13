<?php

namespace Invoke\Meta;

/**
 * Says that type name is generated at runtime.
 *
 * @see \Invoke\Toolkit\Validators\ArrayOf
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
