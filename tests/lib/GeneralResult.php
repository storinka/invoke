<?php

namespace InvokeTests;

use Invoke\Typesystem\Result;
use Invoke\Typesystem\Type;

class GeneralResult extends Result
{
    public static function params(): array
    {
        return [
            "T" => Type::T,
            "bool" => Type::Bool,
            "int" => Type::Int,
            "float" => Type::Float,
            "string" => Type::String,
            "null" => Type::Null,
            "array" => Type::Array,
        ];
    }
}
