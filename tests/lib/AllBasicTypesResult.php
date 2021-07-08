<?php

namespace InvokeTests;

use Invoke\Typesystem\Result;
use Invoke\Typesystem\Types;

class AllBasicTypesResult extends Result
{
    public static function params(): array
    {
        return [
            "T" => Types::T,
            "bool" => Types::Bool,
            "int" => Types::Int,
            "float" => Types::Float,
            "string" => Types::String,
            "null" => Types::Null,
            "array" => Types::Array,
        ];
    }
}
