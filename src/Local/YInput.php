<?php

namespace Invoke\Local;

use Invoke\Typesystem\Input;
use Invoke\Typesystem\Type;

class YInput extends Input
{
    public static function params(): array
    {
        return [
            "y" => Type::Null(Type::Float),
        ];
    }
}
