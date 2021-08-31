<?php

namespace InvokeTests;

use Invoke\Typesystem\Type;
use Invoke\Typesystem\Types;

class AllBasicTypesType extends Type
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

            "notType" => NotType::class,
        ];
    }
}
