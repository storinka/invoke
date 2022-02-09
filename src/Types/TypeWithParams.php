<?php

namespace Invoke\Types;

use Invoke\Container\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Exceptions\RequiredParamNotProvidedException;
use Invoke\Pipe;
use Invoke\Pipeline;
use Invoke\Stop;
use Invoke\Support\HasUsedTypes;
use Invoke\Type;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use ReflectionParameter;
use ReflectionProperty;
use RuntimeException;
use function invoke_get_class_name;

class TypeWithParams implements Type, HasUsedTypes
{
    /**
     * @param mixed $input
     * @return static
     */
    public function pass(mixed $input): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        $reflectionClass = ReflectionUtils::getClass($this::class);

        $parameters = $this->_validateParameters(
            $reflectionClass->getProperties(),
            $input
        );

        foreach ($parameters as $name => $value) {
            $this->{$name} = $value;
        }

        return $this;
    }

    public static function getName(): string
    {
        return invoke_get_class_name(static::class);
    }

    public function getUsedTypes(): array
    {
        return ReflectionUtils::extractPipesFromParamsPipe($this);
    }

    protected function setParamValue(string $name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * @param ReflectionParameter[]|ReflectionProperty[] $parameters
     * @param mixed $input
     * @return array
     */
    protected function _validateParameters(array $parameters, mixed $input): array
    {
        $rendered = [];
        if (method_exists($this, "render")) {
            $rendered = $this->render($input);
        }

        if (is_array($input)) {
            $inputType = $input["@type"] ?? null;

            if ($inputType) {
                if ($inputType !== Utils::getPipeTypeName($this::class)) {
                    throw new InvalidTypeException($this, $inputType);
                }
            }
        }

        $validated = [];

        foreach ($parameters as $parameter) {
            if (ReflectionUtils::isPropertyDependency($parameter)) {
                $name = $parameter->getName();
                $type = $parameter->getType()->getName();

                $value = Container::getInstance()->get($type);

                $this->setParamValue($name, $value);

                continue;
            }

            if (!ReflectionUtils::isPropertyParam($parameter)) {
                continue;
            }

            $name = $parameter->getName();
            $type = $parameter->getType();

            $pipe = ReflectionUtils::extractPipeFromReflectionType($type);

            if (!Utils::isPipeType($pipe)) {
                throw new RuntimeException("Only type pipes allowed.");
            }

            $value = null;

            $hasDefaultValue = $parameter instanceof ReflectionParameter
                ? $parameter->isDefaultValueAvailable()
                : $parameter->getDefaultValue();

            // check if param was rendered
            if (array_key_exists($name, $rendered)) {
                $value = $rendered[$name];
            } else {
                // if not rendered, try to extract value from input
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
                            // if default value is not provided, throw an error
                            throw new RequiredParamNotProvidedException($this, $name);
                        }
                    }
                } else if (is_object($input)) {
                    // if input is an object,
                    // check if param exist or try to use default value
                    if (property_exists($input, $name)) {
                        $value = $input->{$name};
                    } else {
                        if ($hasDefaultValue) {
                            continue;
                        } else {
                            throw new RequiredParamNotProvidedException($this, $name);
                        }
                    }
                }
            }

            $className = static::getName();

            $value = Pipeline::catcher(
                fn() => $pipe->pass($value),
                "{$className}::{$name}"
            );

            Pipeline::catcher(
                function () use ($name, $parameter, &$value) {
                    foreach ($parameter->getAttributes() as $attribute) {
                        if (is_subclass_of($attribute->getName(), Pipe::class)) {
                            $attributePipe = $attribute->newInstance();

                            $value = $attributePipe->pass($value);
                        }
                    }
                },
                "{$className}::{$name}"
            );

            $validated[$name] = $value;
        }

        return $validated;
    }
}