<?php

namespace Invoke;

use Invoke\Exceptions\InvalidParamTypeException;
use Invoke\Exceptions\TypesystemValidationException;
use Invoke\Utils\TypeUtils;
use Invoke\Validation\MultipleValidations;

class Typesystem
{
    public static function validateParam(string $paramName,
                                                $paramType,
                                                $value): mixed
    {
        $valueType = gettype($value);

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
            if ($valueType === Types::null) {
                throw new InvalidParamTypeException($paramName, $paramType, $valueType);
            }

            return $value;
        }

        if ($paramType === Types::null) {
            if ($valueType === Types::null) {
                return null;
            }

            throw new InvalidParamTypeException($paramName, $paramType, $valueType);
        }

        if (
            // p is float && v is int
            $paramType === Types::float &&
            $valueType === Types::int
        ) {
            $value = floatval($value);
            $valueType = gettype($value);
        } else if (
            // p is int && v is float
            $paramType === Types::int &&
            $valueType === Types::float
        ) {
            $value = intval($value);
            $valueType = gettype($value);
        }


        if (!Invoke::$config["typesystem"]["strict"]) {
            if (
                // x is int && y is string
                $paramType === Types::int &&
                $valueType === Types::string
            ) {
                if (!preg_match("/^-?[0-9]+$/", $value)) {
                    throw new InvalidParamTypeException($paramName, $paramType, $valueType);
                }

                $value = intval($value);
                $valueType = gettype($value);
            } else if (
                // x is float && y is string
                $paramType === Types::float &&
                $valueType === Types::string
            ) {
                if (!preg_match("/^-?[0-9]+([,.][0-9]+)?$/", $value)) {
                    throw new InvalidParamTypeException($paramName, $paramType, $valueType);
                }

                $value = floatval(str_replace(",", ".", $value));
                $valueType = gettype($value);
            } else if (
                // x is bool && y is int or string
                $paramType === Types::bool &&
                ($valueType === Types::int || $valueType === Types::string)
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
                $paramType === Types::string &&
                ($valueType === Types::int || $valueType === Types::float)
            ) {
                return (string)$value;
            }
        }

        if ($paramType instanceof Validation) {
            return $paramType->validate(
                $paramName,
                $value,
            );
        }

        if (class_exists($paramType)) {
            if (is_subclass_of($paramType, Type::class)) {
                return new $paramType(
                    $paramName,
                    $value
                );
            }

            if (is_array($value)) {
                $paramType = new $paramType;

                TypeUtils::hydrate($paramType, $value);

                return $paramType;
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


    public static function validateParams(array $params,
                                                $data,
                                          array $rendered = []): array
    {
        $result = [];

        foreach ($params as $paramName => $paramType) {
            $value = null;

            // check if params were rendered manually
            if (array_key_exists($paramName, $rendered)) {
                $value = $rendered[$paramName];
            } else if (is_object($data)) {
                if ($data instanceof AsData) { // if data implements AsData, we use params from getDataParams
                    $data = $data->getDataParams();

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

            $result[$paramName] = Typesystem::validateParam(
                $paramName,
                $paramType,
                $value
            );
        }

        return $result;
    }

    public static function getTypeName($type): string
    {
        if ($type instanceof MultipleValidations) {
            $type = $type->getType();
        }

        if (is_array($type)) {
            if (invoke_is_assoc($type)) {
                return "map";
            }

            return implode(" | ", array_map(fn($t) => Typesystem::getTypeName($t), $type));
        }

        switch ($type) {
            case Types::T:
                return "T";

            case "int":
            case "integer":
            case Types::int:
                return "int";

            case "string":
            case Types::string:
                return "string";

            case "float":
            case "double":
            case Types::float:
                return "float";

            case Types::array:
                return "array";

            case "bool":
            case "boolean":
            case Types::bool:
                return "bool";

            case null:
            case Types::null:
                return "null";
        }

        if (is_string($type) && class_exists($type)) {
            return invoke_get_class_name($type);
        }

        return $type;
    }

    public static function isBuiltinType($type): bool
    {
        return in_array($type, [Types::null, Types::int, Types::string, Types::bool, Types::float, Types::array], true);
    }
}