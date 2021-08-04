<?php

namespace Invoke\Typesystem;

use Invoke\InvokeMachine;
use Invoke\Typesystem\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem\Exceptions\TypesystemValidationException;

class Typesystem
{
    public static function validateParams(array $params, $data, array $rendered = []): array
    {
        $result = [];

        foreach ($params as $paramName => $paramType) {
            $value = null;

            // check if params were rendered manually
            if (array_key_exists($paramName, $rendered)) {
                $value = $rendered[$paramName];
            } else if (is_object($data)) {
                if ($data instanceof HasInvokeParams) { // if data implements HasInvokeParams, we use them
                    $data = $data->getInvokeParams();

                    if (array_key_exists($paramName, $data)) {
                        $value = $data[$paramName];
                    }
                } else if (property_exists($data, $paramName)) { // either way we try to get the value directly
                    $value = $data->{$paramName};
                }
            } else if (
                is_array($data) && // if data is an array, the
                array_key_exists($paramName, $data)
            ) {
                $value = $data[$paramName];
            }

            $result[$paramName] = Typesystem::validateParam($paramName, $paramType, $value);
        }

        return $result;
    }

    public static function validateParam(string $paramName, $paramType, $value)
    {
        $valueType = gettype($value);

        // if paramType is array then we check if value is matching any type of its items
        if (is_array($paramType)) {
            foreach ($paramType as $orParamType) {
                try {
                    return Typesystem::validateParam($paramName, $orParamType, $value);
                } catch (TypesystemValidationException $e) {
                    // ignore
                }
            }

            throw new InvalidParamTypeException($paramName, $paramType, $valueType);
        }

        if ($paramType === Types::T) {
            if ($valueType === Types::Null) {
                throw new InvalidParamTypeException($paramName, $paramType, $valueType);
            }

            return $value;
        }

        if ($paramType === Types::Null) {
            if ($valueType === Types::Null) {
                return null;
            }

            throw new InvalidParamTypeException($paramName, $paramType, $valueType);
        }

        if (
            // x is float && y is int
            $paramType === Types::Float &&
            $valueType === Types::Int
        ) {
            $value = floatval($value);
            $valueType = gettype($value);
        } else if (
            // x is int && y is float
            $paramType === Types::Int &&
            $valueType === Types::Float
        ) {
            $value = intval($value);
            $valueType = gettype($value);
        }

        if (!InvokeMachine::configuration("strict", true)) {
            if (
                // x is int && y is string
                $paramType === Types::Int &&
                $valueType === Types::String
            ) {
                if (!preg_match("/^-?[0-9]+$/", $value)) {
                    throw new InvalidParamTypeException($paramName, $paramType, $valueType);
                }

                $value = intval($value);
                $valueType = gettype($value);
            } else if (
                // x is float && y is string
                $paramType === Types::Float &&
                $valueType === Types::String
            ) {
                if (!preg_match("/^-?[0-9]+([,.][0-9]+)?$/", $value)) {
                    throw new InvalidParamTypeException($paramName, $paramType, $valueType);
                }

                $value = floatval(str_replace(",", ".", $value));
                $valueType = gettype($value);
            } else if (
                // x is bool && y is int or string
                $paramType === Types::Bool &&
                ($valueType === Types::Int || $valueType === Types::String)
            ) {
                // prob should be removed checking by string
                if ($value === "true" || $value === "1" || $value === 1) {
                    $value = true;
                } else if ($value === "false" || $value === "0" || $value === 0) {
                    $value = false;
                }

                $valueType = gettype($value);
            } else if (
                // x is string && y is int or float
                $paramType === Types::String &&
                ($valueType === Types::Int || $valueType === Types::Float)
            ) {
                return (string)$value;
            }
        }

        if ($paramType instanceof CustomType) {
            $value = Typesystem::validateParam(
                $paramName,
                $paramType->getBaseType(),
                $value
            );

            return $paramType->validate($paramName, $value);
        }

        if (class_exists($paramType)) {
            if (is_array($value)) {
                // todo: input_to_array

                return new $paramType($value);
            }

            if (!is_object($value)) {
                throw new InvalidParamTypeException($paramName, $paramType, $valueType);
            }

            $actualClass = get_class($value);

            if ($actualClass !== $paramType) {
                throw new InvalidParamTypeException($paramName, $paramType, $valueType);
            }

            return $value;
        }

        if ($paramType !== $valueType) {
            throw new InvalidParamTypeException($paramName, $paramType, $valueType);
        }

        return $value;
    }

    public static function getTypeAsString($type): string
    {
        if ($type instanceof CustomType) {
            return $type->toString();
        }

        if (is_array($type)) {
            return implode(" | ", array_map(fn($t) => Typesystem::getTypeAsString($t), $type));
        }

        return Typesystem::getTypeName($type);
    }

    public static function getTypeName($type): string
    {
        if ($type instanceof CustomType) {
            $type = $type->getBaseType();
        }

        if (is_array($type)) {
            // todo: throw an exception
            if (invoke_is_assoc($type)) {
                return Types::T;
            }

            return implode(" | ", array_map(fn($t) => Typesystem::getTypeName($t), $type));
        }

        switch ($type) {
            case Types::T:
                return "T";

            case "int":
            case "integer":
            case Types::Int:
                return "int";

            case "string":
            case Types::String:
                return "string";

            case "float":
            case "double":
            case Types::Float:
                return "float";

            case Types::Array:
                return "array";

            case "bool":
            case "boolean":
            case Types::Bool:
                return "bool";

            case null:
            case Types::Null:
                return "null";
        }

        if (is_string($type) && class_exists($type)) {
            if (is_subclass_of($type, InvokeType::class)) {
                return invoke_get_class_name($type);
            }
        }

        return $type;
    }

    public static function isSimpleType($type): bool
    {
        return in_array($type, [Types::Null, Types::Int, Types::String, Types::Bool, Types::Float, Types::Array], true);
    }
}
