<?php

namespace InvokeTests\Lib;

use Invoke\Typesystem\Input;
use Invoke\Typesystem\Types;

class UndefAndNullInput extends Input
{
    public static function params(): array
    {
        return [
            "undefOrInt" => Types::Undef(Types::Int),
            "nullOrInt" => Types::Null(Types::Int),
        ];
    }
}
