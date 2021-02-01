<?php

namespace InvokeTests;

use Invoke\Typesystem\Input;
use Invoke\Typesystem\Type;

class UndefAndNullInput extends Input
{
    public static function params(): array
    {
        return [
            "undefOrInt" => Type::Undef(Type::Int),
            "nullOrInt" => Type::Null(Type::Int),
        ];
    }
}
