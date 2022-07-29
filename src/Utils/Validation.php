<?php

namespace Invoke\Utils;

use ArrayAccess;
use Invoke\Attributes\Parameter;
use Invoke\Container;
use Invoke\Container\Inject;
use Invoke\Exceptions\PipeException;
use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\Pipe;
use Invoke\Piping;
use ReflectionParameter;
use RuntimeException;

final class Validation
{
    public static function validateReflectionParameters(array $reflectionParameters,
                                                        array $inputParameters,
                                                        bool  $handleInject = true): array
    {
        $validated = [];

        foreach ($reflectionParameters as $reflectionParameter) {
            $name = $reflectionParameter->getName();
            $type = $reflectionParameter->getType();

            if ($handleInject) {
                if (Validation::isReflectionParameterInjectable($reflectionParameter)) {
                    $dependency = Container::get($type->getName());

                    $validated[$name] = $dependency;

                    continue;
                }
            }

            if (!Validation::isReflectionParameterValidParameter($reflectionParameter)) {
                continue;
            }

            $value = null;

            if (is_array($inputParameters) && array_key_exists($name, $inputParameters)) {
                $value = $inputParameters[$name];
            } elseif ($inputParameters instanceof ArrayAccess && isset($inputParameters[$name])) {
                $value = $inputParameters[$name];
            } elseif (is_object($inputParameters) && property_exists($inputParameters, $name)) {
                $value = $inputParameters->{$name};
            } else {
                if ($reflectionParameter->isDefaultValueAvailable()) {
                    continue;
                } else {
                    if (!$type->allowsNull()) {
                        throw new RequiredParameterNotProvidedException($name);
                    }
                }
            }

            $validated[$name] = Validation::validateReflectionParameter($reflectionParameter, $value);
        }

        return $validated;
    }

    public static function validateReflectionParameter(ReflectionParameter $reflectionParameter, mixed $value)
    {
        $name = $reflectionParameter->getName();
        $type = $reflectionParameter->getType();

        if (!Validation::isReflectionParameterValidParameter($reflectionParameter)) {
            throw new PipeException("Cannot validate \"$name\" because it is not a parameter.");
        }

        $typePipe = ReflectionUtils::extractPipeFromReflectionType($type);

        if (!Utils::isPipeType($typePipe)) {
            throw new RuntimeException("Cannot validate \"$name\" because its type is not a pipe.");
        }

        $value = Piping::catcher(
            fn() => Piping::run($typePipe, $value),
            "{$name}"
        );

        // do not run attributes if value is null and is valid
        if (!$reflectionParameter->allowsNull() || $value !== null) {
            Piping::catcher(
                function () use ($name, $reflectionParameter, &$value) {
                    foreach ($reflectionParameter->getAttributes() as $attribute) {
                        if (is_subclass_of($attribute->getName(), Pipe::class)) {
                            $attributePipe = $attribute->newInstance();

                            $value = Piping::run($attributePipe, $value);
                        }
                    }
                },
                "{$name}"
            );
        }

        return $value;
    }

    public static function isReflectionParameterValidParameter(ReflectionParameter $reflectionParameter): bool
    {
        foreach ($reflectionParameter->getAttributes() as $attribute) {
            if ($attribute->getName() === Parameter::class || is_subclass_of($attribute->getName(), Parameter::class)) {
                return true;
            }
        }

        return true;
    }

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