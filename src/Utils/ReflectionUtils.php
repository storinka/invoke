<?php

namespace Invoke\Utils;

use Invoke\Attributes\NotParameter;
use Invoke\Method;
use Invoke\Pipe;
use Invoke\Pipes\AnyPipe;
use Invoke\Pipes\ClassPipe;
use Invoke\Pipes\NullPipe;
use Invoke\Pipes\ParamsPipe;
use Invoke\Pipes\UnionPipe;
use Invoke\Utils;
use Invoke\Validator;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Reflector;

class ReflectionUtils
{
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
                $type = Utils::typeToPipe($reflectionType->getName());
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

            $validators = [];

            foreach ($property->getAttributes() as $attribute) {
                if (is_subclass_of($attribute->getName(), Validator::class)) {
                    $validationPipe = $attribute->newInstance();

                    $validators[] = $validationPipe;
                }
            }

            $params[] = [
                "name" => $name,
                "type" => $pipe,
                "isOptional" => $isOptional,
                "defaultValue" => $defaultValue,
                "validators" => $validators,
            ];
        }

        return $params;
    }

    public static function extractPipeFromReturnType(ReflectionMethod $method): Pipe
    {
        $reflectionReturnType = $method->getReturnType();

        return ReflectionUtils::extractPipeFromReflectionType($reflectionReturnType);
    }

    public static function extractPipesFromParamsPipe(ParamsPipe|string $pipe): array
    {
        $pipes = [];

        $reflectionClass = new ReflectionClass($pipe);
        $params = ReflectionUtils::extractParamsPipes($reflectionClass);
        foreach ($params as $param) {
            $pipes[] = $param["type"];

            /** @var Validator $validator */
            foreach ($param["validators"] as $validator) {
                array_push($pipes, ...$validator->getUsedPipes());
            }
        }
        
        if ($reflectionClass->isSubclassOf(Method::class)) {
            $reflectionMethod = $reflectionClass->getMethod("handle");

            $pipes[] = ReflectionUtils::extractPipeFromReturnType($reflectionMethod);
        }

        return $pipes;
    }
}
