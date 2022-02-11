<?php

namespace Invoke\Support;

use Invoke\Types\TypeWithParams;

/**
 * @mixin TypeWithParams
 */
trait ReadonlyParams
{
    protected function invoke_setParamValue($name, $value)
    {
        $this->{$name} = $value;
    }
}
