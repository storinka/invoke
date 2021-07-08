<?php

namespace Invoke\Typesystem;

use Invoke\Typesystem\CustomTypes\IntCustomType;
use Invoke\Typesystem\CustomTypes\NullOrDefaultValueCustomType;
use Invoke\Typesystem\CustomTypes\RegexCustomType;
use Invoke\Typesystem\CustomTypes\StringCustomType;
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

    public static function String(int $minLength = null, $maxLength = null)
    {
        if (is_null($minLength) && is_null($maxLength)) {
            return Types::String;
        }

        return new StringCustomType($minLength, $maxLength);
    }

    public static function Int(int $min = null, int $max = null)
    {
        if (is_null($min) && is_null($max)) {
            return Types::Int;
        }

        return new IntCustomType($min, $max);
    }

    public static function Regex(string $pattern): RegexCustomType
    {
        return new RegexCustomType($pattern);
    }
}
