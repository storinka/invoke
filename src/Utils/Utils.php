<?php

namespace Invoke\Utils;

use Exception;
use Invoke\Data;
use Invoke\Pipe;
use Invoke\Support\HasDynamicName;
use Invoke\Support\HasUsedTypes;
use Invoke\Support\Singleton;
use Invoke\Type;
use Invoke\Types\AnyType;
use Invoke\Types\ArrayType;
use Invoke\Types\BinaryType;
use Invoke\Types\BoolType;
use Invoke\Types\EnumType;
use Invoke\Types\FloatType;
use Invoke\Types\IntType;
use Invoke\Types\NullType;
use Invoke\Types\StringType;
use Invoke\Types\UnionType;
use Invoke\Types\WrappedType;
use Invoke\Validator;
use function invoke_get_class_name;

/**
 * Common utils.
 */
class Utils
{
    public static function getMethodNameFromClass(string $class): string
    {
        $name = invoke_get_class_name($class);

        $first = mb_strtolower($name[0]);
        $rest = substr($name, 1);

        return "$first$rest";
    }

    public static function getErrorNameFromException(Exception|string $exception): string
    {
        if (is_string($exception)) {
            $className = $exception;
        } else {
            $className = $exception::class;
        }

        $className = invoke_get_class_name($className);

        if (str_ends_with($className, "Exception")) {
            $className = substr($className, 0, strlen($className) - 9);
        }

        $name = static::camelToUnderscore($className, "_");

        return strtoupper($name);
    }

    public static function camelToUnderscore($string, $us = "-"): string
    {
        return strtolower(preg_replace(
            '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/', $us, $string));
    }

    public static function isPipeTypeBuiltin(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return in_array(
            $class,
            [
                IntType::class,
                StringType::class,
                FloatType::class,
                StringType::class,
                NullType::class,
                ArrayType::class,
                BoolType::class
            ]
        );
    }

    public static function isPipeTypeData(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return $class === Data::class || is_subclass_of($class, Data::class);
    }

    public static function isPipeTypeUnion(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return $class === UnionType::class || is_subclass_of($class, UnionType::class);
    }

    public static function isPipeTypeBinary(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return $class === BinaryType::class || is_subclass_of($class, BinaryType::class);
    }

    public static function isPipeTypeEnum(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return $class === EnumType::class || is_subclass_of($class, EnumType::class);
    }

    public static function isPipeTypeValidator(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return $class === Validator::class || is_subclass_of($class, Validator::class);
    }

    public static function toType(Type|string|array $something): Type
    {
        if (is_array($something)) {
            return new UnionType($something);
        } else if (is_string($something)) {
            if (class_exists($something)) {
                if (is_subclass_of($something, Singleton::class)) {
                    return $something::getInstance();
                }

                return new WrappedType($something);
            } else {
                return Utils::typeNameToPipe($something);
            }
        } else {
            return $something;
        }
    }

    public static function typeNameToPipe(string $type): Type
    {
        return match ($type) {
            "int", "integer" => IntType::getInstance(),
            "float", "double" => FloatType::getInstance(),
            "bool", "boolean" => BoolType::getInstance(),
            "array" => ArrayType::getInstance(),
            "null", "NULL" => NullType::getInstance(),
            "string" => StringType::getInstance(),
            default => AnyType::getInstance(),
        };
    }

    public static function isPipeType(Pipe|string $pipe): bool
    {
        if ($pipe instanceof WrappedType) {
            $pipe = $pipe->typeClass;
        }

        if ($pipe instanceof Type) {
            return true;
        }

        if (is_string($pipe) && class_exists($pipe)) {
            if (is_subclass_of($pipe, Type::class)) {
                return true;
            }
        }

        return false;
    }

    public static function getValueTypeName(mixed $value): string
    {
        if ($value instanceof WrappedType) {
            return $value->typeClass;
        }

        if ($value instanceof Pipe) {
            return $value::class;
        }

        $type = gettype($value);

        return static::getNormalizedTypeName($type);
    }

    public static function getNormalizedTypeName(string $typeName): string
    {
        return match ($typeName) {
            "int", "integer" => "int",
            "float", "double" => "float",
            "bool", "boolean" => "bool",
            "array" => "array",
            "null", "NULL" => "null",
            "string" => "string",
            default => $typeName,
        };
    }

    public static function extractUsedTypes(HasUsedTypes|string $pipe): array
    {
        $pipes = [];

        if (is_string($pipe)) {
            $pipe = new WrappedType($pipe);
        }

        if ($pipe instanceof HasUsedTypes) {
            foreach ($pipe->invoke_getUsedTypes() as $usedType) {
                $pipes[] = $usedType;

                if ($usedType instanceof HasUsedTypes) {
                    array_push($pipes, ...static::extractUsedTypes($usedType));
                }
            }
        }

        return $pipes;
    }

    public static function getPipeTypeName(Type|string $pipe): string
    {
        if (is_string($pipe) && class_exists($pipe) && is_subclass_of($pipe, Type::class)) {
            return $pipe::invoke_getName();
        }

        if ($pipe instanceof HasDynamicName) {
            return $pipe->invoke_getDynamicName();
        }

        return $pipe::invoke_getName();
    }

    public static function getSchemaTypeName(Type $type): string
    {
        if ($type instanceof WrappedType) {
            $class = $type->typeClass;
        } else if ($type instanceof EnumType) {
            $class = $type->enumClass;
        } else {
            $class = $type::class;
        }

        $typeName = $type::invoke_getName();

        if ($type instanceof HasDynamicName) {
            $typeName = $type->invoke_getDynamicName();
        }

        return "{$class}:[{$typeName}]";
    }

    public static function isNullable(Pipe|string $pipe): bool
    {
        if ($pipe instanceof UnionType) {
            foreach ($pipe->pipes as $uPipe) {
                if (static::isNullable($uPipe)) {
                    return true;
                }
            }
        }

        if ($pipe instanceof WrappedType) {
            $pipe = $pipe->typeClass;
        } elseif (!is_string($pipe)) {
            $pipe = $pipe::class;
        }

        if (is_string($pipe)) {
            return is_subclass_of($pipe, NullType::class)
                || is_subclass_of($pipe, AnyType::class)
                || $pipe === NullType::class
                || $pipe === AnyType::class;
        }

        return false;
    }
}