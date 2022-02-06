<?php

namespace Invoke\Pipes;

use Invoke\AbstractPipe;
use Invoke\Exceptions\RequiredParamNotProvidedException;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Pipeline;
use Invoke\Utils\ReflectionUtils;
use Invoke\Validation;
use ReflectionClass;

class ParamsPipe extends AbstractPipe
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
                if ($inputType !== $this->getTypeName()) {
                    throw new ValidationFailedException(new ClassPipe(static::class), $inputType);
                }
            }
        }

        $class = new ReflectionClass($this);
        $properties = $class->getProperties();

        foreach ($properties as $property) {
            if (!ReflectionUtils::isPropertyParam($property)) {
                continue;
            }

            $name = $property->getName();
            $pipe = ReflectionUtils::extractPipeFromReflectionType($property->getType());

            $value = null;

            if (array_key_exists($name, $rendered)) {
                $value = $rendered[$name];
            } else {
                if (is_array($input)) {
                    if (array_key_exists($name, $input)) {
                        $value = $input[$name];
                    } else {
                        if ($property->hasDefaultValue()) {
                            continue;
                        } else {
                            throw new RequiredParamNotProvidedException($this, $name);
                        }
                    }
                } else if (is_object($input)) {
                    if (!property_exists($input, $name)) {
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
                "{$this->getTypeName()}::{$name}"
            );

            foreach ($property->getAttributes() as $attribute) {
                if (is_subclass_of($attribute->getName(), Validation::class)) {
                    $validationPipe = $attribute->newInstance();

                    $validationPipe->parentPipe = $this;
                    $validationPipe->paramName = $name;

                    $value = $validationPipe->pass($value);
                }
            }

            $this->{$name} = $value;
        }

        return $this;
    }
}