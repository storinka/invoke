<?php

namespace InvokeTests;

use Invoke\Typesystem\Type;
use Invoke\V1\Typesystem\InputV1;

class UndefAndNullInput extends InputV1
{
    public static function params(): array
    {
        return [
            "undefOrInt" => Type::Undef(Type::Int),
            "nullOrInt" => Type::Null(Type::Int),
        ];
    }
}
