<?php

namespace Invoke\Utils;

use Invoke\Meta\NotParameter;
use Invoke\HasUsedTypes;
use Invoke\Method;
use Invoke\Type;
use Invoke\Types\AnyType;
use Invoke\Types\EnumType;
use Invoke\Types\NullType;
use Invoke\Types\TypeWithParams;
use Invoke\Types\UnionType;
use Invoke\Types\WrappedType;
use Invoke\Validator;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Reflector;

/**
 * Common utils to work with reflection.
 */
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

    public static function extractPipeFromReflectionType(ReflectionNamedType|ReflectionUnionType|null $reflectionType): Type
    {
        if ($reflectionType == null) {
            return AnyType::getInstance();
        } else if ($reflectionType instanceof ReflectionNamedType) {
            $name = $reflectionType->getName();

            if ($reflectionType->isBuiltin()) {
                $type = Utils::typeNameToPipe($name);
            } else if (enum_exists($name) && !is_subclass_of($name, Type::class)) {
                return new EnumType($name);
            } else {
                $type = new WrappedType($name);
            }

            if ($reflectionType->allowsNull()) {
                return new UnionType([NullType::getInstance(), $type]);
            }

            return $type;
        } else if ($reflectionType instanceof ReflectionUnionType) {
            return new UnionType(array_map(
                fn($t) => static::extractPipeFromReflectionType($t),
                $reflectionType->getTypes()
            ));
        }

        return new WrappedType($reflectionType->getName());
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

    public static function extractPipeFromMethodReturnType(ReflectionMethod $method): Type
    {
        $reflectionReturnType = $method->getReturnType();

        return ReflectionUtils::extractPipeFromReflectionType($reflectionReturnType);
    }

    public static function extractPipesFromParamsPipe(TypeWithParams|string $pipe): array
    {
        $pipes = [];

        $reflectionClass = new ReflectionClass($pipe);
        $params = ReflectionUtils::extractParamsPipes($reflectionClass);
        foreach ($params as $param) {
            $pipes[] = $param["type"];

            /** @var Validator $validator */
            foreach ($param["validators"] as $validator) {
                if ($validator instanceof HasUsedTypes) {
                    array_push($pipes, ...$validator->getUsedTypes());
                }
            }
        }

        if ($reflectionClass->isSubclassOf(Method::class)) {
            $reflectionMethod = $reflectionClass->getMethod("handle");

            $pipes[] = ReflectionUtils::extractPipeFromMethodReturnType($reflectionMethod);
        }

        return $pipes;
    }
}
