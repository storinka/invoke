<?php

namespace Invoke\Utils;

use Invoke\Attributes\Parameter;
use Invoke\Container\Inject;
use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\NewMethod\Information\ParameterInformationInterface;
use Invoke\Piping;
use ReflectionParameter;

final class Validation
{
    /**
     * @param array<ParameterInformationInterface> $parametersInformation
     * @param array $inputParameters
     * @return array
     */
    public static function validateParametersInformation(array $parametersInformation,
                                                         array $inputParameters): array
    {
        $parameters = [];

        foreach ($parametersInformation as $parameterInformation) {
            $name = $parameterInformation->getName();

            $value = $inputParameters[$name] ?? null;
            $parameterProvided = array_key_exists($name, $inputParameters);

            $value = Validation::validateParameterInformation(
                $parameterInformation,
                $value,
                $parameterProvided
            );

            $parameters[$name] = $value;
        }

        return $parameters;
    }

    /**
     * @param ParameterInformationInterface $parameterInformation
     * @param mixed $value
     * @param bool $parameterProvided
     * @return mixed
     */
    public static function validateParameterInformation(ParameterInformationInterface $parameterInformation,
                                                        mixed                         $value,
                                                        bool                          $parameterProvided = true): mixed
    {
        $name = $parameterInformation->getName();
        $pipe = $parameterInformation->getPipe();
        $hasDefaultValue = $parameterInformation->hasDefaultValue();
        $nullable = $parameterInformation->isNullable();

        if (!$parameterProvided) {
            if ($hasDefaultValue) {
                return $parameterInformation->getDefaultValue();
            } else {
                if (!$nullable) {
                    throw new RequiredParameterNotProvidedException($name);
                }
            }
        }

        $value = Piping::catcher(
            fn() => Piping::run($pipe, $value),
            "{$name}",
        );

        if (!$parameterInformation->isNullable() || $value !== null) {
            Piping::catcher(
                function () use ($parameterInformation, &$value) {
                    foreach ($parameterInformation->getValidators() as $validator) {
                        $value = Piping::run($validator, $value);
                    }
                },
                "{$name}"
            );
        }

        return $value;
    }

    /**
     * @param ReflectionParameter $reflectionParameter
     * @return bool
     */
    public static function isReflectionParameterValidParameter(ReflectionParameter $reflectionParameter): bool
    {
        foreach ($reflectionParameter->getAttributes() as $attribute) {
            if ($attribute->getName() === Parameter::class || is_subclass_of($attribute->getName(), Parameter::class)) {
                return true;
            }
        }

        return true;
    }

    /**
     * @param ReflectionParameter $reflectionParameter
     * @return bool
     */
    public static function isReflectionParameterInjectable(ReflectionParameter $reflectionParameter): bool
    {
        foreach ($reflectionParameter->getAttributes() as $attribute) {
            if ($attribute->getName() === Inject::class) {
                return true;
            }
        }

        return false;
    }
}