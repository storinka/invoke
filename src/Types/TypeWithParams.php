<?php

namespace Invoke\Types;

use Invoke\Container\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Exceptions\RequiredParamNotProvidedException;
use Invoke\Pipe;
use Invoke\Pipeline;
use Invoke\Support\HasUsedTypes;
use Invoke\Type;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use ReflectionClass;
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

        $class = new ReflectionClass($this);
        $properties = $class->getProperties();

        foreach ($properties as $property) {
            if (ReflectionUtils::isPropertyDependency($property)) {
                $name = $property->getName();
                $type = $property->getType()->getName();

                $value = Container::getInstance()->get($type);

                $this->setParamValue($name, $value);

                continue;
            }

            if (!ReflectionUtils::isPropertyParam($property)) {
                continue;
            }

            $name = $property->getName();
            $type = $property->getType();

            $pipe = ReflectionUtils::extractPipeFromReflectionType($type);

            if (!Utils::isPipeType($pipe)) {
                throw new RuntimeException("Only type pipes allowed.");
            }

            $value = null;

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
                        if ($property->hasDefaultValue()) {
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
                        if ($property->hasDefaultValue()) {
                            continue;
                        } else {
                            throw new RequiredParamNotProvidedException($this, $name);
                        }
                    }
                }
            }

            $value = Pipeline::catcher(
                fn() => $pipe->pass($value),
                "{$class->name}::{$name}"
            );

            Pipeline::catcher(
                function () use ($name, $property, &$value) {
                    foreach ($property->getAttributes() as $attribute) {
                        if (is_subclass_of($attribute->getName(), Pipe::class)) {
                            $attributePipe = $attribute->newInstance();

                            $value = $attributePipe->pass($value);
                        }
                    }
                },
                "{$class->name}::{$name}"
            );

            $this->setParamValue($name, $value);
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
}