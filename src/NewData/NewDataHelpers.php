<?php

namespace Invoke\NewData;

use Invoke\Container;
use Invoke\NewMethod\Information\ParameterInformation;
use Invoke\Pipe;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Validation;

/**
 * @mixin NewData
 */
trait NewDataHelpers
{
    use WithCachedParametersInformation;

    private function asInvokeExtractParametersInformation(): array
    {
        $parameters = [];

        $reflectionClass = ReflectionUtils::getClass($this::class);
        $reflectionProperties = $reflectionClass->getProperties();

        foreach ($reflectionProperties as $reflectionProperty) {
            if (!Validation::isReflectionPropertyValidParameter($reflectionProperty)) {
                continue;
            }

            $type = $reflectionProperty->getType();

            if (Validation::isReflectionPropertyInjectable($reflectionProperty)) {
                $reflectionProperty->setValue(Container::get($type->getName()));
            }

            $name = $reflectionProperty->getName();
            $pipe = ReflectionUtils::extractPipeFromReflectionType($type);
            $hasDefaultValue = $reflectionProperty->hasDefaultValue();
            $defaultValue = $hasDefaultValue ? $reflectionProperty->getDefaultValue() : null;
            $nullable = $type->allowsNull();

            $validators = [];

            foreach ($reflectionProperty->getAttributes() as $attribute) {
                if (is_subclass_of($attribute->getName(), Pipe::class)) {
                    $attributePipe = $attribute->newInstance();

                    $validators[] = $attributePipe;
                }
            }

            $parameters[] = new ParameterInformation(
                name: $name,
                pipe: $pipe,
                nullable: $nullable,
                hasDefaultValue: $hasDefaultValue,
                defaultValue: $defaultValue,
                validators: $validators,
            );
        }

        return $parameters;
    }
}