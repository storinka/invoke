<?php

namespace Invoke\Support;

use Closure;
use Invoke\Container;
use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\Pipe;
use Invoke\Piping;
use Invoke\Stop;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use ReflectionFunction;
use RuntimeException;

class FunctionPipe implements Pipe
{
    public Closure|string $function;

    public function __construct(Closure|string $function)
    {
        $this->function = $function;
    }

    public function pass(mixed $input): mixed
    {
        if ($input instanceof Stop) {
            return $input;
        }

        $reflectionFunction = new ReflectionFunction($this->function);

        $functionArgs = [];

        foreach ($reflectionFunction->getParameters() as $reflectionParameter) {
            $name = $reflectionParameter->getName();
            $type = $reflectionParameter->getType();

            if (ReflectionUtils::isPropertyDependency($reflectionParameter)) {
                $dependency = Container::get($type->getName());

                $functionArgs[] = $dependency;

                continue;
            }

            if (!ReflectionUtils::isPropertyParameter($reflectionParameter)) {
                continue;
            }

            $value = null;

            $hasDefaultValue = $reflectionParameter->isDefaultValueAvailable();

            // try to extract value from input
            // so, if input is an array, we check if param is provided
            if (is_array($input)) {
                if (array_key_exists($name, $input)) {
                    $value = $input[$name];
                } else {
                    // if param is not provided via input
                    // check if it has default value
                    if ($hasDefaultValue) {
                        // if default value is provided, then just check next param
                        continue;
                    } else {
                        if (!$type->allowsNull()) {
                            throw new RequiredParameterNotProvidedException($name);
                        }
                    }
                }
            } elseif (is_object($input)) {
                // if input is an object,
                // check if param exist or try to use default value
                if (property_exists($input, $name)) {
                    $value = $input->{$name};
                } else {
                    if ($hasDefaultValue) {
                        continue;
                    } else {
                        if (!$type->allowsNull()) {
                            throw new RequiredParameterNotProvidedException($name);
                        }
                    }
                }
            }

            $typePipe = ReflectionUtils::extractPipeFromReflectionType($type);

            if (!Utils::isPipeType($typePipe)) {
                throw new RuntimeException("Cannot validate \"$name\" because its type is not a pipe.");
            }

            $value = Piping::catcher(
                fn() => Piping::run($typePipe, $value),
                "{$name}"
            );

            Piping::catcher(
                function () use ($name, $reflectionParameter, &$value) {
                    foreach ($reflectionParameter->getAttributes() as $attribute) {
                        if (is_subclass_of($attribute->getName(), Pipe::class)) {
                            $attributePipe = $attribute->newInstance();

                            $value = Piping::run($attributePipe, $value);
                        }
                    }
                },
                "{$name}->{$name}"
            );

            $functionArgs[] = $value;
        }

        return $reflectionFunction->invokeArgs($functionArgs);
    }
}
