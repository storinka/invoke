<?php

namespace Invoke\NewMethod;

use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\Pipe;
use Invoke\Piping;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Validation;

/**
 * @mixin NewMethod
 */
trait NewMethodHelpers
{
    /**
     * @return array<string, ParameterInformationInterface>
     */
    private function extractParametersInformation(): array
    {
        $parameters = [];

        $reflectionClass = ReflectionUtils::getClass($this::class);
        $handleMethod = $reflectionClass->getMethod("handle");

        $reflectionParameters = $handleMethod->getParameters();

        foreach ($reflectionParameters as $reflectionParameter) {
            $type = $reflectionParameter->getType();

            $name = $reflectionParameter->getName();
            $pipe = ReflectionUtils::extractPipeFromReflectionType($type);
            $hasDefaultValue = $reflectionParameter->isDefaultValueAvailable();
            $defaultValue = $hasDefaultValue ? $reflectionParameter->getDefaultValue() : null;
            $nullable = $type->allowsNull();
            $injectable = Validation::isReflectionParameterInjectable($reflectionParameter);

            if (!$injectable && !Validation::isReflectionParameterValidParameter($reflectionParameter)) {
                continue;
            }

            $validators = [];

            foreach ($reflectionParameter->getAttributes() as $attribute) {
                if (is_subclass_of($attribute->getName(), Pipe::class)) {
                    $attributePipe = $attribute->newInstance();

                    $validators[] = $attributePipe;
                }
            }

            $parameters[] = new ParameterInformationInformation(
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