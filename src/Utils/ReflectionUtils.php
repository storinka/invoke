<?php

namespace Invoke\Utils;

use Invoke\Invoke;
use Invoke\Meta\Inject;
use Invoke\Meta\MethodExtension;
use Invoke\Meta\MethodTraitExtension;
use Invoke\Meta\NotParameter;
use Invoke\Method;
use Invoke\Support\HasUsedTypes;
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
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;
use Reflector;

/**
 * Common utils to work with reflection.
 */
final class ReflectionUtils
{
    protected static array $cachedClasses;
    protected static array $cachedMethodTraitExtensions;
    protected static array $cachedMethodAttributeExtensions;

    public static function getClass(string $class): ReflectionClass
    {
        if (empty(static::$cachedClasses[$class])) {
            static::$cachedClasses[$class] = new ReflectionClass($class);
        }

        return static::$cachedClasses[$class];
    }

    /**
     * @param string $methodClass
     * @return array
     */
    public static function extractMethodTraitExtensions(string $methodClass): array
    {
        if (isset(static::$cachedMethodTraitExtensions[$methodClass])) {
            return static::$cachedMethodTraitExtensions[$methodClass];
        }

        static::$cachedMethodTraitExtensions[$methodClass] = [];

        foreach (class_uses_deep($methodClass) as $trait) {
            $reflectionTrait = ReflectionUtils::getClass($trait);

            if ($reflectionTrait->getAttributes(MethodTraitExtension::class)) {
                static::$cachedMethodTraitExtensions[$methodClass][] = $trait;
            }
        }

        return static::$cachedMethodTraitExtensions[$methodClass];
    }

    /**
     * @param string $methodClass
     * @return MethodExtension[]
     */
    public static function extractMethodAttributeExtensions(string $methodClass): array
    {
        if (isset(ReflectionUtils::$cachedMethodAttributeExtensions[$methodClass])) {
            return ReflectionUtils::$cachedMethodAttributeExtensions[$methodClass];
        }

        ReflectionUtils::$cachedMethodAttributeExtensions[$methodClass] = [];

        $reflectionClass = ReflectionUtils::getClass($methodClass);
        foreach ($reflectionClass->getAttributes() as $attribute) {
            if (is_subclass_of($attribute->getName(), MethodExtension::class)) {
                ReflectionUtils::$cachedMethodAttributeExtensions[$methodClass][] = $attribute->newInstance();
            }
        }

        $classTraits = $reflectionClass->getTraits();
        foreach ($classTraits as $classTrait) {
            foreach ($classTrait->getAttributes() as $attribute) {
                if (is_subclass_of($attribute->getName(), MethodExtension::class)) {
                    ReflectionUtils::$cachedMethodAttributeExtensions[$methodClass][] = $attribute->newInstance();
                }
            }
        }

        return ReflectionUtils::$cachedMethodAttributeExtensions[$methodClass];
    }

    public static function callMethodExtensionsHook(Method $method, string $hook, array $params = [])
    {
        $methodReflectionClass = ReflectionUtils::getClass($method::class);

        $traitExtensions = ReflectionUtils::extractMethodTraitExtensions($method::class);

        foreach ($traitExtensions as $trait) {
            $traitName = invoke_get_class_name($trait);
            $methodName = "{$hook}{$traitName}";

            if (method_exists($method, $methodName)) {
                $traitMethod = $methodReflectionClass->getMethod($methodName);

                $traitMethod->invokeArgs($method, $params);
            }
        }

        $attributeExtensions = ReflectionUtils::extractMethodAttributeExtensions($method::class);
        $attributeExtensions = [...$attributeExtensions, ...Invoke::getMethodExtensions()];

        foreach ($attributeExtensions as $extension) {
            $extensionReflectionClass = ReflectionUtils::getClass($extension::class);

            if (method_exists($extension, $hook)) {
                $extensionHookMethod = $extensionReflectionClass->getMethod($hook);

                $extensionHookMethod->invokeArgs($extension, [$method, ...$params]);
            }
        }
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

    public static function extractPipeFromReflectionType(ReflectionNamedType|ReflectionUnionType|null $reflectionType): Type
    {
        if ($reflectionType == null) {
            return AnyType::getInstance();
        } elseif ($reflectionType instanceof ReflectionNamedType) {
            $name = $reflectionType->getName();

            if ($reflectionType->isBuiltin()) {
                $type = Utils::typeNameToPipe($name);
            } elseif (enum_exists($name) && !is_subclass_of($name, Type::class)) {
                return new EnumType($name);
            } else {
                $type = new WrappedType($name);
            }

            if ($reflectionType->allowsNull()) {
                if ($name === "mixed") {
                    return AnyType::getInstance();
                } else {
                    return new UnionType([NullType::getInstance(), $type]);
                }
            }

            return $type;
        } elseif ($reflectionType instanceof ReflectionUnionType) {
            return new UnionType(array_map(
                fn($t) => static::extractPipeFromReflectionType($t),
                $reflectionType->getTypes()
            ));
        }

        return new WrappedType($reflectionType->getName());
    }

    public static function isPropertyParam(ReflectionProperty|ReflectionParameter $property): bool
    {
        if ($property instanceof ReflectionProperty) {
            if (!$property->isPublic() || $property->isStatic()) {
                return false;
            }
        }

        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === NotParameter::class || is_subclass_of($attribute->getName(), NotParameter::class)) {
                return false;
            }
        }

        return true;
    }

    public static function isPropertyDependency(ReflectionProperty|ReflectionParameter $property): bool
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() === Inject::class) {
                return true;
            }
        }

        return false;
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

        $reflectionClass = ReflectionUtils::getClass($pipe);
        $params = ReflectionUtils::extractParamsPipes($reflectionClass);
        foreach ($params as $param) {
            $pipes[] = $param["type"];

            /** @var Validator $validator */
            foreach ($param["validators"] as $validator) {
                if ($validator instanceof HasUsedTypes) {
                    array_push($pipes, ...$validator->invoke_getUsedTypes());
                }
            }
        }

        if ($reflectionClass->isSubclassOf(Method::class)) {
            $reflectionMethod = $reflectionClass->getMethod("handle");

            $pipes[] = ReflectionUtils::extractPipeFromMethodReturnType($reflectionMethod);
        }

        return $pipes;
    }

    public static function validateReflectionParameters(array $reflectionParameters,
                                                        array $inputParameters,
                                                        array $renderedParameters)
    {
    }
}
