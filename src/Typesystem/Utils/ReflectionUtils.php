<?php

namespace Invoke\Typesystem\Utils;

use Invoke\Typesystem\CustomTypes\NullOrDefaultValueCustomType;
use Invoke\Typesystem\Types;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use RuntimeException;

class ReflectionUtils
{
    public static function mapReflectionPropertyToParam(ReflectionProperty $reflectionProperty, $object): array
    {
        $paramName = $reflectionProperty->getName();

        $defaultValue = null;
        if ($object && isset($object->{$paramName})) {
            $defaultValue = $object->{$paramName};
        }

        $paramType = ReflectionUtils::mapReflectionTypeToParamType($reflectionProperty->getType(), $defaultValue);

        return [$paramName, $paramType];
    }

    public static function mapReflectionTypeToParamType(ReflectionType $reflectionType, $defaultValue = null)
    {
        $allowsNull = $reflectionType->allowsNull();

        if ($reflectionType instanceof ReflectionNamedType) {
            $paramType = $reflectionType->getName();
        } else {
            $paramType = (string)$reflectionType;
        }

        // check if property type is builtin, and if so, use it
        if ($reflectionType->isBuiltin()) {
            $paramType = ReflectionUtils::getBasicTypeFromBuiltin($paramType);
        }

        if ($allowsNull) {
            if ($paramType[0] === "?") {
                // get actual type
                $paramType = substr($paramType, 1);
            }

            // if there is default value in the object, we use it
            if (isset($defaultValue)) {
                $paramType = Types::Null($paramType, $defaultValue);
            } else {
                // if no default value, just say that the type nullable
                $paramType = Types::Null($paramType);
            }
        }

        return $paramType;
    }

    public static function inspectInvokeFunctionReflectionClassParams(ReflectionClass $reflectionClass)
    {
        $actualClass = $reflectionClass->name;
        $params = $actualClass::params();

        // todo: document this thing

        $reflectionMethod = $reflectionClass->getMethod("handle");
        $reflectionParameters = $reflectionMethod->getParameters();

        foreach ($reflectionParameters as $reflectionParameter) {
            $hasDefault = $reflectionParameter->isDefaultValueAvailable();
            $allowsNull = $reflectionParameter->allowsNull();

            $reflParamName = $reflectionParameter->getName();
            $reflParamType = $reflectionParameter->getType();

            if ($reflParamType instanceof ReflectionNamedType && $reflParamType->isBuiltin()) {
                $reflParamType = ReflectionUtils::getBasicTypeFromBuiltin($reflParamType->getName());
            } else {
                $reflParamType = $reflParamType->getName();
            }

            if ($hasDefault) {
                $reflParamType = new NullOrDefaultValueCustomType($reflParamType, $reflectionParameter->getDefaultValue());
            }
            if ($allowsNull) {
                $reflParamType = Types::Null($reflParamType);
            }

            if ($reflParamName !== "params" && !array_key_exists($reflParamName, $params)) {
                $params[$reflParamName] = $reflParamType;
            }
        }

        return $params;
    }

    public static function inspectInvokeTypeReflectionClassParams(ReflectionClass $reflectionClass, $object = null): array
    {
        $actualClass = $reflectionClass->name;

        // the type params
        $params = [];

        // map class properties to params
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            [$name, $type] = ReflectionUtils::mapReflectionPropertyToParam($reflectionProperty, $object);

            $params = array_merge($params, [$name => $type]);
        }

        // merge params() result with params
        $params = array_merge($params, $actualClass::params());

        return $params;
    }

    public static function getBasicTypeFromBuiltin(string $builtin): string
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

    public static function parseComment(ReflectionClass $reflectionClass): array
    {
        $comment = [
            "summary" => null,
            "description" => null
        ];

        $docComment = $reflectionClass->getDocComment();
        $docBlockFactory = DocBlockFactory::createInstance();

        if ($docComment) {
            $docBlock = $docBlockFactory->create($docComment);

            $comment["summary"] = $docBlock->getSummary();
            $comment["description"] = $docBlock->getDescription();
        }

        return $comment;
    }
}
