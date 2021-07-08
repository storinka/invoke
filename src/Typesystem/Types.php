<?php

namespace Invoke\Typesystem;

use Invoke\Typesystem\CustomTypes\NullOrDefaultValueCustomType;
use Invoke\Typesystem\CustomTypes\TypedArrayCustomType;

class Types
{
    public const T = "T";

    public const Null = "NULL";

    public const Bool = "boolean";
    public const Int = "integer";
    public const Float = "double";
    public const String = "string";
    public const Array = "array";

    public static function Some(...$of): array
    {
        return $of;
    }

    public static function Null($type, $defaultValue = null)
    {
        if ($defaultValue) {
            return new NullOrDefaultValueCustomType($type, $defaultValue);
        }

        return Types::Some(Types::Null, $type);
    }

    public static function ArrayOf($type = Types::String,
                                   ?int $minSize = null,
                                   ?int $maxSize = null): TypedArrayCustomType
    {
        return new TypedArrayCustomType($type, $minSize, $maxSize);
    }
}
