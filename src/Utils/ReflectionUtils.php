<?php

namespace Invoke\Utils;

use Ds\Set;
use Invoke\Attributes\NotParameter;
use Invoke\Attributes\Parameter;
use Invoke\Container;
use Invoke\Container\Inject;
use Invoke\Extensions\MethodExtension;
use Invoke\Extensions\MethodTraitExtension;
use Invoke\Invoke;
use Invoke\Method;
use Invoke\Support\HasUsedTypes;
use Invoke\Support\TypeWithParams;
use Invoke\Type;
use Invoke\Types\AnyType;
use Invoke\Types\EnumType;
use Invoke\Types\NullType;
use Invoke\Types\UnionType;
use Invoke\Types\WrappedType;
use Invoke\Validator;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionProperty;
use ReflectionUnionType;

/**
 * Common utils to work with reflection.
 *
 * ...to be rewritten
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
     * @return class-string[]
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

    public static function extractComment(ReflectionFunctionAbstract|ReflectionProperty|ReflectionClass|ReflectionClassConstant $reflectionClass): array
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

    public static function isPropertyParameter(ReflectionProperty|ReflectionParameter $property): bool
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

            if ($attribute->getName() === Parameter::class || is_subclass_of($attribute->getName(), Parameter::class)) {
                return true;
            }
        }

        $onlyWithAttribute = Container::get(Invoke::class)->getConfig("parameters.onlyWithAttribute", false);

        if ($onlyWithAttribute && $property instanceof ReflectionProperty) {
            return false;
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

        $propertiesOrParameters = [];

        if ($class->isSubclassOf(Method::class)) {
            if (Container::get(Invoke::class)->getConfig("methods.usePropertiesAsParameters", true)) {
                $propertiesOrParameters = $class->getProperties();
            }

            $handleMethod = $class->getMethod("handle");
            array_push($propertiesOrParameters, ...$handleMethod->getParameters());
        } else {
            $propertiesOrParameters = $class->getProperties();
        }

        foreach ($propertiesOrParameters as $property) {
            if (!ReflectionUtils::isPropertyParameter($property)) {
                continue;
            }

            $name = $property->getName();
            $pipe = ReflectionUtils::extractPipeFromReflectionType($property->getType());

            $isOptional = false;
            $defaultValue = null;

            if ($property instanceof ReflectionParameter ? $property->isDefaultValueAvailable() : $property->hasDefaultValue()) {
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

    public static function extractRequiredHeaders(ReflectionClass $reflectionClass): array
    {
        $attributes = $reflectionClass->getAttributes(RequireHeaders::class);

        if (!empty($attributes)) {
            $attribute = $attributes[0];

            $instance = $attribute->newInstance();

            return $instance->headers;
        }

        return [];
    }

    public static function extractUsedPipesFromParamsPipe(TypeWithParams|string $pipe): array
    {
        $pipes = new Set();

        $reflectionClass = ReflectionUtils::getClass($pipe);

        $params = ReflectionUtils::extractParamsPipes($reflectionClass);

        foreach ($params as $param) {
            /** @var Type $type */
            $type = $param["type"];

            /** @var Validator[] $validators */
            $validators = $param["validators"];

            $pipes->add($type);

            foreach ($validators as $validator) {
                if ($pipes->contains($validator)) {
                    continue;
                }

                if ($validator instanceof HasUsedTypes) {
                    $pipes->add(...$validator->invoke_getUsedTypes());
                }
            }
        }

        if ($reflectionClass->isSubclassOf(Method::class)) {
            $reflectionMethod = $reflectionClass->getMethod("handle");

            $pipes[] = ReflectionUtils::extractPipeFromMethodReturnType($reflectionMethod);
        }

        return $pipes->toArray();
    }

    /**
     * @param ReflectionProperty|ReflectionParameter $reflectionPropertyOrParameter
     * @param string $className
     * @return bool
     */
    public static function hasAttribute(ReflectionProperty|ReflectionParameter $reflectionPropertyOrParameter, string $className): bool
    {

        foreach ($reflectionPropertyOrParameter->getAttributes($className) as $attribute) {
            if ($attribute->getName() === $className || is_subclass_of($attribute->getName(), $className)) {
                return true;
            }
        }

        return false;
    }
}
