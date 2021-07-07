<?php

namespace InvokeTests;

use Invoke\V1\Typesystem\ResultV1;
use Invoke\V1\Typesystem\Types;

class AllBasicTypesResult extends ResultV1
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
            "nullOrInt" => Types::Null(Types::Int),
        ];
    }
}
