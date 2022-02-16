<?php

namespace Invoke\Support;

/**
 * Says that type name is generated at runtime.
 */
interface HasDynamicTypeName
{
    /**
     * Dynamically generated type name.
     *
     * @return string
     */
    public function invoke_getDynamicTypeName(): string;
}
