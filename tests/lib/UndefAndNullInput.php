<?php

namespace InvokeTests\Lib;

use Invoke\Typesystemx\Input;
use Invoke\Types;

class UndefAndNullInput extends Input
{
    public static function params(): array
    {
        return [
            "undefOrInt" => Types::Undef(Types::int),
            "nullOrInt" => Types::Null(Types::int),
        ];
    }
}
