<?php

namespace Invoke\Typesystem;

use Invoke\Typesystem\CustomTypes\InArrayCustomType;
use Invoke\Typesystem\CustomTypes\IntCustomType;
use Invoke\Typesystem\CustomTypes\RegexCustomType;
use Invoke\Typesystem\CustomTypes\StringCustomType;
use Invoke\Typesystem\CustomTypes\TypedArrayCustomType;

class Type
{
    public const T = "T";

    public const Undef = "UNDEF";
    public const Null = "NULL";

    public const Bool = "boolean";
    public const Int = "integer";
    public const Float = "double";
    public const String = "string";
    public const Array = "array";

    public const Map = "map";

    public static function Some(...$of): array
    {
        return $of;
    }

    public static function Null($or): array
    {
        return Type::Some(Type::Null, $or);
    }

    public static function Undef($or): array
    {
        return Type::Some(Type::Undef, $or);
    }

    public static function ArrayOf($type = Type::String, $minSize = null, $maxSize = null): TypedArrayCustomType
    {
        return new TypedArrayCustomType($type, $minSize, $maxSize);
    }

    public static function String(int $minLength = null, $maxLength = null)
    {
        if (is_null($minLength) && is_null($maxLength)) {
            return Type::String;
        }

        return new StringCustomType($minLength, $maxLength);
    }

    public static function Int(int $min = null, int $max = null)
    {
        if (is_null($min) && is_null($max)) {
            return Type::Int;
        }

        return new IntCustomType($min, $max);
    }

    public static function In(array $values, $type = Type::String): InArrayCustomType
    {
        return new InArrayCustomType($values, $type);
    }

    public static function Regex(string $pattern): RegexCustomType
    {
        return new RegexCustomType($pattern);
    }
}
