<?php

namespace InvokeTests\Lib;

use Invoke\Typesystemx\Type;
use Invoke\Types;

class AllBasicTypesType extends Type
{
    public static function params(): array
    {
        return [
            "T" => Types::T,
            "bool" => Types::bool,
            "int" => Types::int,
            "float" => Types::float,
            "string" => Types::string,
            "null" => Types::null,
            "array" => Types::array,

            "notType" => NotType::class,
        ];
    }
}
