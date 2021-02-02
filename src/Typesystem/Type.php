<?php

namespace Invoke\Typesystem;

use RuntimeException;

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

    public static function ArrayOf($type, $min = 0, $max = null): CustomType
    {
        return new CustomType(Type::Array, function ($paramName, $value) use ($max, $min, $type) {
            $size = sizeof(array_values($value));

            if ($size < $min) {
                throw new RuntimeException("INVALID_PARAM_ARRAY_SIZE_VALUE");
            }

            if ($max && $size > $max) {
                throw new RuntimeException("INVALID_PARAM_ARRAY_SIZE_VALUE");
            }

            foreach ($value as $i => $v) {
                $value[$i] = Typesystem::validateParam("{$paramName}[{$i}]", $type, $v);
            }

            return $value;
        }, "Array<" . Typesystem::getTypeName($type) . ">");
    }

    public static function String(int $minLength = 0, $maxLength = null): CustomType
    {
        return new CustomType(Type::String, function ($paramName, $value) use ($maxLength, $minLength) {
            if (!is_null($minLength)) {
                if (strlen($value) < $minLength) {
                    throw new RuntimeException("INVALID_PARAM_STRING_VALUE");
                }
            }

            if (!is_null($maxLength)) {
                if (strlen($value) > $maxLength) {
                    throw new RuntimeException("INVALID_PARAM_STRING_VALUE");
                }
            }

            return $value;
        }, "String");
    }

    public static function Int(int $min = null, int $max = null): CustomType
    {
        return new CustomType(Type::Int, function ($paramName, $value) use ($min, $max) {
            if (!is_null($min)) {
                if ($value < $min) {
                    throw new RuntimeException("INVALID_PARAM_INT_VALUE");
                }
            }

            if (!is_null($max)) {
                if ($value > $max) {
                    throw new RuntimeException("INVALID_PARAM_INT_VALUE");
                }
            }

            return $value;
        }, "Int");
    }

    public static function In(array $values, $type = Type::String): CustomType
    {
        return new CustomType($type, function ($paramName, $value) use ($values) {
            if (!in_array($value, $values)) {
                throw new RuntimeException("INVALID_PARAM_VALUES");
            }

            return $value;
        }, fn() => "In(" . implode(", ", $values) . ")");
    }

    public static function Regex(string $pattern): CustomType
    {
        return new CustomType(Type::String, function ($paramName, $value) use ($pattern) {
            if (!preg_match($pattern, $value)) {
                throw new RuntimeException("INVALID_REGEX_VALUE");
            }

            return $value;
        }, fn() => "Regex($pattern)");
    }

    public static function getStringTypeRepresentation($type)
    {
        if ($type instanceof CustomType) {
            return $type->type;
        }

        if (is_array($type)) {
            if (invoke_is_assoc($type)) {
                return $type;
            } else {
                return implode(" | ", array_map(fn($type) => Type::getStringTypeRepresentation($type), $type));
            }
        }

        return Type::getProperTypeName($type);
    }

    public static function getProperTypeName($type)
    {
        if ($type instanceof CustomType) {
            return Type::getProperTypeName($type->type);
        }

        if (is_array($type)) {
            $type = array_map(fn($t) => Type::getProperTypeName($t), $type);
        }

        switch ($type) {
            case Type::Int:
                return "Int";
            case Type::String:
                return "String";
            case Type::Float:
                return "Float";
            case Type::Array:
                return "Array";
            case Type::Bool:
                return "Bool";
            case Type::Undef:
                return "Undef";

            case Null:
            case "NULL":
                return "Null";
        }

        if (is_string($type) && class_exists($type)) {
            return invoke_get_class_name($type);
        }

        return $type;
    }
}
