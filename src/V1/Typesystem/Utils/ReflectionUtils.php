<?php

namespace Invoke\V1\Typesystem\Utils;

use Invoke\V1\Typesystem\Types;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;

class ReflectionUtils
{
    public static function mapReflectionPropertyToParam(ReflectionProperty $reflectionProperty, $object): array
    {
        $paramName = $reflectionProperty->getName();
        $paramType = null;

        $reflectionType = $reflectionProperty->getType();

        // check if property type is builtin, and if so, use it
        if ($reflectionType->isBuiltin()) {
            $paramType = ReflectionUtils::getInvokeTypeFromBuiltin((string)$reflectionType);
        } else if ($reflectionType instanceof ReflectionNamedType) {
            $paramType = ReflectionUtils::getInvokeTypeFromBuiltin((string)$reflectionType);
        }

        // if the type starts with ? symbol, then we suppose it it nullable type
        if ($paramType[0] === "?") {
            // get actual type
            $paramType = substr($paramType, 1);

            // if there is default value in the object, we use it
            if (isset($object->{$paramName})) {
                $paramType = Types::Null($paramType, $object->{$paramName});
            } else if ($reflectionType->allowsNull()) {
                // if no default value, just say that the type nullable
                $paramType = Types::Null($paramType);
            }
        }

        return [$paramName, $paramType];
    }

    public static function getInvokeTypeFromBuiltin(string $builtin): string
    {
        switch ($builtin) {
            case "int":
            case "integer":
                return Types::Int;
            case "float":
            case "double":
                return Types::Float;
            case "bool":
            case "boolean":
                return Types::Bool;
            case "array":
                return Types::Array;
            case "null":
                return Types::Null;
            case "string":
                return Types::String;
        }

        throw new RuntimeException("Unsupported built-in type: $builtin");
    }
}
