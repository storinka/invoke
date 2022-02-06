<?php

namespace Invoke\Utils;

use Invoke\Attributes\NotParameter;
use Invoke\Pipe;
use Invoke\Pipes\AnyPipe;
use Invoke\Pipes\ArrayPipe;
use Invoke\Pipes\BoolPipe;
use Invoke\Pipes\ClassPipe;
use Invoke\Pipes\FloatPipe;
use Invoke\Pipes\IntPipe;
use Invoke\Pipes\NullPipe;
use Invoke\Pipes\StringPipe;
use Invoke\Pipes\UnionPipe;
use Invoke\Validation;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Reflector;

class ReflectionUtils
{
    public static function typeToPipe(string $type): Pipe
    {
        return match ($type) {
            "int", "integer" => IntPipe::getInstance(),
            "float", "double" => FloatPipe::getInstance(),
            "bool", "boolean" => BoolPipe::getInstance(),
            "array" => ArrayPipe::getInstance(),
            "null", "NULL" => NullPipe::getInstance(),
            "string" => StringPipe::getInstance(),
            default => AnyPipe::getInstance(),
        };

    }

    public static function extractComment(Reflector $reflectionClass): array
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

    public static function extractPipeFromReflectionType(ReflectionNamedType|ReflectionUnionType|null $reflectionType): Pipe
    {
        if ($reflectionType == null) {
            return AnyPipe::getInstance();
        } else if ($reflectionType instanceof ReflectionNamedType) {
            if ($reflectionType->isBuiltin()) {
                $type = static::typeToPipe($reflectionType->getName());
            } else {
                $type = new ClassPipe($reflectionType->getName());
            }

            if ($reflectionType->allowsNull()) {
                return new UnionPipe([NullPipe::getInstance(), $type]);
            }

            return $type;
        } else if ($reflectionType instanceof ReflectionUnionType) {
            return new UnionPipe(array_map(
                fn($t) => static::extractPipeFromReflectionType($t),
                $reflectionType->getTypes()
            ));
        }

        return new ClassPipe($reflectionType->getName());
    }

    public static function isPropertyParam(ReflectionProperty $property): bool
    {
        if (!$property->isPublic() || $property->isStatic()) {
            return false;
        }

        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === NotParameter::class || is_subclass_of($attribute->getName(), NotParameter::class)) {
                return false;
            }
        }

        return true;
    }

    public static function extractParamsPipes(ReflectionClass $class): array
    {
        $params = [];

        foreach ($class->getProperties() as $property) {
            if (!static::isPropertyParam($property)) {
                continue;
            }

            $name = $property->getName();
            $pipe = ReflectionUtils::extractPipeFromReflectionType($property->getType());

            $isOptional = false;
            $defaultValue = null;

            if ($property->hasDefaultValue()) {
                $isOptional = true;
                $defaultValue = $property->getDefaultValue();
            }

            $validations = [];

            foreach ($property->getAttributes() as $attribute) {
                if (is_subclass_of($attribute->getName(), Validation::class)) {
                    $validationPipe = $attribute->newInstance();

                    $validationPipe->parentPipe = $pipe;
                    $validationPipe->paramName = $name;

                    $validations[] = $validationPipe;
                }
            }

            $params[] = [
                "name" => $property->name,
                "type" => $pipe,
                "isOptional" => $isOptional,
                "defaultValue" => $defaultValue,
                "validations" => $validations,
            ];
        }

        return $params;
    }
}
