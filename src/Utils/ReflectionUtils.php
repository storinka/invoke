<?php

namespace Invoke\Utils;

use Invoke\NotParameter;
use Invoke\Types;
use Invoke\Validation;
use Invoke\Validations\NullOrDefault;
use Invoke\Validations\TypeWithValidations;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use Reflector;
use RuntimeException;

class ReflectionUtils
{
    public static function normalizeBuiltinType(string $builtin): string
    {
        switch ($builtin) {
            case "int":
            case "integer":
                return Types::int;
            case "float":
            case "double":
                return Types::float;
            case "bool":
            case "boolean":
                return Types::bool;
            case "array":
                return Types::array;
            case "null":
                return Types::null;
            case "string":
                return Types::string;
        }

        throw new RuntimeException("Unsupported built-in type: $builtin");
    }

    public static function parseComment(Reflector $reflectionClass): array
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
            $comment["description"] = $docBlock->getDescription()->render();
        }

        return $comment;
    }

    /**
     * @param ReflectionProperty[]|ReflectionParameter[] $parameters
     * @return array
     */
    public static function reflectionParamsOrPropsToInvoke(array $parameters): array
    {
        $params = [];

        foreach ($parameters as $parameter) {
            if ($parameter instanceof ReflectionProperty) {
                if (!$parameter->isPublic()) {
                    continue;
                }
            }

            $name = $parameter->getName();
            $type = $parameter->getType();

            $validations = [];
            $notParameter = false;

            if ($parameter instanceof ReflectionParameter) {
                if ($parameter->allowsNull() && $parameter->isDefaultValueAvailable()) {
                    $validations[] = new NullOrDefault($parameter->getDefaultValue());
                }
            }

            foreach ($parameter->getAttributes() as $attribute) {
                if ($attribute->getName() === NotParameter::class || is_subclass_of($attribute->getName(), NotParameter::class)) {
                    $notParameter = true;
                    break;
                }

                if (is_subclass_of($attribute->getName(), Validation::class)) {
                    $validations[] = $attribute->newInstance();
                }
            }

            if ($notParameter) {
                continue;
            }

            if (!$type) {
                $params[$parameter->name] = Types::T;
            }

            $params[$name] = static::reflectionTypeToInvoke($type);

            if (!empty($validations)) {
                $params[$name] = new TypeWithValidations($params[$name], $validations);
            }
        }

        return $params;
    }

    public static function reflectionTypeToInvoke(ReflectionNamedType|ReflectionUnionType|null $reflectionType): array|string
    {
        if ($reflectionType == null) {
            return Types::T;
        } else if ($reflectionType instanceof ReflectionNamedType) {
            if ($reflectionType->isBuiltin()) {
                $type = static::normalizeBuiltinType($reflectionType->getName());
            } else {
                $type = $reflectionType->getName();
            }

            if ($reflectionType->allowsNull()) {
                return [Types::null, $type];
            }

            return $type;
        } else if ($reflectionType instanceof ReflectionUnionType) {
            return array_map(
                fn($t) => static::reflectionTypeToInvoke($t),
                $reflectionType->getTypes()
            );
        }

        return $reflectionType->getName();
    }
}