<?php

namespace Invoke\V1\Typesystem;

use Invoke\Typesystem\Type;
use Invoke\V1\Typesystem\CustomTypes\NullOrDefaultValueCustomTypeV1;
use Invoke\V1\Typesystem\CustomTypes\TypedArrayCustomTypeV1;

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
            return new NullOrDefaultValueCustomTypeV1($type, $defaultValue);
        }

        return Types::Some(Types::Null, $type);
    }

    public static function ArrayOf($type = Type::String,
                                   ?int $minSize = null,
                                   ?int $maxSize = null): TypedArrayCustomTypeV1
    {
        return new TypedArrayCustomTypeV1($type, $minSize, $maxSize);
    }
}
