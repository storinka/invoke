<?php

namespace Invoke\Utils;

use Ds\Set;
use Exception;
use Invoke\Data;
use Invoke\Pipe;
use Invoke\Support\BinaryType;
use Invoke\Support\HasDynamicTypeName;
use Invoke\Support\HasToArray;
use Invoke\Support\HasUsedTypes;
use Invoke\Support\Singleton;
use Invoke\Type;
use Invoke\Types\AnyType;
use Invoke\Types\ArrayType;
use Invoke\Types\BoolType;
use Invoke\Types\EnumType;
use Invoke\Types\FloatType;
use Invoke\Types\IntType;
use Invoke\Types\NullType;
use Invoke\Types\StringType;
use Invoke\Types\UnionType;
use Invoke\Types\WrappedType;
use Invoke\Validator;
use function array_map;
use function get_object_vars;
use function gettype;
use function is_array;

/**
 * Common utils.
 *
 * ...to be rewritten
 */
class Utils
{
    public static function getMethodNameFromClass(string $class): string
    {
        $name = get_class_name($class);

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

        $className = get_class_name($className);

        if (str_ends_with($className, "Exception")) {
            $className = substr($className, 0, strlen($className) - 9);
        }

        $name = static::camelToUnderscore($className, "_");

        return strtoupper($name);
    }

    public static function camelToUnderscore($string, $us = "-"): string
    {
        return strtolower(preg_replace(
            '/(?<=\d)(?=[A-Za-z])|(?<=[A-Za-z])(?=\d)|(?<=[a-z])(?=[A-Z])/',
            $us,
            $string
        ));
    }

    public static function isPipeTypeSimple(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return in_array(
            $class,
            [
                IntType::class,
                StringType::class,
                FloatType::class,
                NullType::class,
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

    public static function isPipeTypeArray(Pipe|string $pipe): bool
    {
        $class = is_string($pipe) ? $pipe : $pipe::class;

        return $class === ArrayType::class || is_subclass_of($class, ArrayType::class);
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
        } elseif (is_string($something)) {
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

    public static function extractUsedTypes(HasUsedTypes|string $pipe, Set $set = new Set()): array
    {
        if (is_string($pipe)) {
            $pipe = new WrappedType($pipe);
        }

        if ($pipe instanceof HasUsedTypes) {
            foreach ($pipe->invoke_getUsedTypes() as $usedType) {
                if ($set->contains($usedType)) {
                    continue;
                }

                if ($usedType === $pipe) {
                    continue;
                }

                if ($usedType instanceof WrappedType) {
                    if (is_string($pipe) && $pipe === $usedType->typeClass) {
                        continue;
                    }

                    if (!$set
                        ->filter(fn($t) => $t instanceof WrappedType && $usedType->typeClass === $t->typeClass)
                        ->isEmpty()
                    ) {
                        continue;
                    }
                } else {
                    if (is_string($pipe) && $pipe === $usedType::class) {
                        continue;
                    }
                }

                $set->add($usedType);

                if ($usedType instanceof HasUsedTypes) {
                    $set->add(...static::extractUsedTypes($usedType, $set));
                }
            }
        }

        $filteredPipes = $set->filter(function (Type $usedType) use ($set) {
            if ($usedType instanceof WrappedType) {
                return $set
                    ->filter(fn($type) => $type instanceof WrappedType && $usedType->typeClass === $type->typeClass)
                    ->isEmpty();
            }

            return true;
        });

        return $filteredPipes->toArray();
    }

    public static function getPipeTypeName(Type|string $pipe): string
    {
        if (is_string($pipe) && class_exists($pipe) && is_subclass_of($pipe, Type::class)) {
            return $pipe::invoke_getTypeName();
        }

        if ($pipe instanceof HasDynamicTypeName) {
            return $pipe->invoke_getDynamicTypeName();
        }

        return $pipe::invoke_getTypeName();
    }

    public static function getUniqueTypeName(Type $type): string
    {
        if ($type instanceof WrappedType) {
            $class = $type->typeClass;
        } elseif ($type instanceof EnumType) {
            $class = $type->enumClass;
        } else {
            $class = $type::class;
        }

        $typeName = $type::invoke_getTypeName();

        if ($type instanceof HasDynamicTypeName) {
            $typeName = $type->invoke_getDynamicTypeName();
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

    public static function isTypeNameBuiltin(string $typeName): bool
    {
        return in_array(
            Utils::getNormalizedTypeName($typeName),
            [
                "int",
                "float",
                "bool",
                "array",
                "null",
                "string",
            ]
        );
    }

    public static function valueToArray(mixed $value): mixed
    {
        if ($value instanceof HasToArray) {
            $value = $value->toArray();
        } else {
            $builtInType = gettype($value);

            if ($builtInType === "object") {
                $value = get_object_vars($value);
            }
        }

        if (is_array($value)) {
            return array_map(fn($item) => static::valueToArray($item), $value);
        }

        return $value;
    }
}
