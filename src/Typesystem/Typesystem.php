<?php

namespace Invoke\Typesystem;

use Invoke\InvokeMachine;

class Typesystem
{
    /**
     * @param string $paramName
     * @param mixed $paramType
     * @param mixed $value
     *
     * @return mixed
     */
    public static function validateParam(
        string $paramName,
        $paramType,
        $value
    )
    {
        $valueType = gettype($value);

        if ($value instanceof Undef) {
            $valueType = Type::Undef;
        }

        if (is_array($paramType)) {
            foreach ($paramType as $orParamType) {
                try {
                    return Typesystem::validateParam($paramName, $orParamType, $value);
                } catch (TypesystemValidationException $e) {
                    // ignore
                }
            }

            throw new TypesystemValidationException($paramName, $paramType, $valueType);
        }

        if ($paramType === Type::T) {
            return $value;
        }

        if ($paramType === Type::Undef) {
            if ($value instanceof Undef) {
                return $value;
            }

            throw new TypesystemValidationException($paramName, $paramType, $valueType);
        }

        if ($paramType === Type::Null) {
            if ($valueType === Type::Null || $value instanceof Undef) {
                return null;
            }

            throw new TypesystemValidationException($paramName, $paramType, $valueType);
        }

        if (!InvokeMachine::configuration("strict")) {
            if (
                // x is int && y is string
                $paramType === Type::Int &&
                $valueType === Type::String
            ) {
                if (!preg_match("/^-?[0-9]+$/", $value)) {
                    throw new TypesystemValidationException($paramName, $paramType, $valueType);
                }

                $value = intval($value);
                $valueType = gettype($value);
            } else if (
                // x is float && y is string
                $paramType === Type::Float &&
                $valueType === Type::String
            ) {
                if (!preg_match("/^-?[0-9]+([,.][0-9]+)?$/", $value)) {
                    throw new TypesystemValidationException($paramName, $paramType, $valueType);
                }

                $value = floatval(str_replace(",", ".", $value));
                $valueType = gettype($value);
            } else if (
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
            } else if (
                // x is bool && y is int or string
                $paramType === Type::Bool &&
                ($valueType === Type::Int || $valueType === Type::String)
            ) {
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

        if ($paramType === Type::Map) {
            if (is_array($value) && (invoke_is_assoc($value) || empty($value))) {
                return $value;
            }

            throw new TypesystemValidationException($paramName, $paramType, $valueType);
        }

        if ($paramType instanceof CustomType) {
            return $paramType->validate($paramName, $value);
        }

        if (class_exists($paramType)) {
            if ($paramType === Input::class || is_subclass_of($paramType, Input::class)) {
                $inputType = new $paramType($value);

                return $inputType->getValidatedAttributes();
            }

            if (!is_object($value)) {
                throw new TypesystemValidationException($paramName, $paramType, $valueType);
            }

            $actualClass = get_class($value);

            if ($actualClass !== $paramType) {
                throw new TypesystemValidationException($paramName, $paramType, $valueType);
            }

            return $value;
        }

        if ($paramType !== $valueType) {
            throw new TypesystemValidationException($paramName, $paramType, $valueType);
        }

        return $value;
    }

    public static function getTypeName($type): string
    {
        if ($type instanceof CustomType) {
            return $type->string;
        }

        if (is_array($type)) {
            if (invoke_is_assoc($type)) {
                return "ASSOC";
            }

            $type = implode(" | ", array_map(fn($t) => Typesystem::getTypeName($t), $type));
        }

        if ($type instanceof Undef) {
            return "Undef";
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
            case null:
            case Type::Null:
                return "Null";

            case Type::Undef:
                return "Undef";

            case Type::Map:
                return "Map";
        }

        if (is_string($type) && class_exists($type)) {
            return invoke_get_class_name($type);
        }

        return $type;
    }
}
