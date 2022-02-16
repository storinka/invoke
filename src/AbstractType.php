<?php

namespace Invoke;

use function Invoke\Utils\get_class_name;

abstract class AbstractType implements Type
{
    public static function invoke_getTypeName(): string
    {
        return get_class_name(static::class);
    }
}