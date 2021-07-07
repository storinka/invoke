<?php

namespace Invoke\V1\Typesystem;

use Invoke\InvokeMachine;
use Invoke\Typesystem\Exceptions\InvalidParamTypeException;
use Invoke\Typesystem\Exceptions\TypesystemValidationException;
use Invoke\Typesystem\Type;
use Invoke\Typesystem\Typesystem;

class TypesystemV1
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

            $result[$paramName] = TypesystemV1::validateParam($paramName, $paramType, $value);
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

        if ($paramType === Type::T) {
            if ($valueType === Type::Null) {
                throw new InvalidParamTypeException($paramName, $paramType, $valueType);
            }

            return $value;
        }

        if ($paramType === Type::Null) {
            if ($valueType === Type::Null) {
                return null;
            }

            throw new InvalidParamTypeException($paramName, $paramType, $valueType);
        }

        if (
            // x is float && y is int
            $paramType === Type::Float &&
            $valueType === Type::Int
        ) {
            $value = floatval($value);
            $valueType = gettype($value);
        } else if (
            // x is int && y is float
            $paramType === Type::Int &&
            $valueType === Type::Float
        ) {
            $value = intval($value);
            $valueType = gettype($value);
        }

        if (!InvokeMachine::configuration("strict", true)) {
            if (
                // x is int && y is string
                $paramType === Type::Int &&
                $valueType === Type::String
            ) {
                if (!preg_match("/^-?[0-9]+$/", $value)) {
                    throw new InvalidParamTypeException($paramName, $paramType, $valueType);
                }

                $value = intval($value);
                $valueType = gettype($value);
            } else if (
                // x is float && y is string
                $paramType === Type::Float &&
                $valueType === Type::String
            ) {
                if (!preg_match("/^-?[0-9]+([,.][0-9]+)?$/", $value)) {
                    throw new InvalidParamTypeException($paramName, $paramType, $valueType);
                }

                $value = floatval(str_replace(",", ".", $value));
                $valueType = gettype($value);
            } else if (
                // x is bool && y is int or string
                $paramType === Type::Bool &&
                ($valueType === Type::Int || $valueType === Type::String)
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
                $paramType === Type::String &&
                ($valueType === Type::Int || $valueType === Type::Float)
            ) {
                return (string)$value;
            }
        }

        if ($paramType instanceof CustomTypeV1) {
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

    public static function getTypeName($type): string
    {
        if ($type instanceof CustomTypeV1) {
            return $type->toString();
        }

        if (is_array($type)) {
            // todo: throw an exception
            if (invoke_is_assoc($type)) {
                return Type::T;
            }

            return implode(" | ", array_map(fn($t) => TypesystemV1::getTypeName($t), $type));
        }

        switch ($type) {
            case "int":
            case "integer":
            case Type::Int:
                return "Int";

            case Type::String:
                return "String";

            case "float":
            case "double":
            case Type::Float:
                return "Float";

            case Type::Array:
                return "Array";

            case "bool":
            case "boolean":
            case Type::Bool:
                return "Bool";

            case null:
            case Type::Null:
                return "Null";
        }

        throw new \RuntimeException("Invalid type: {$type}");
    }
}
