<?php

namespace Invoke\NewMethod;

use Invoke\Exceptions\RequiredParameterNotProvidedException;
use Invoke\Piping;
use RuntimeException;

abstract class AbstractNewMethod implements MethodInterface
{
    /**
     * @var ParameterInformationInterface[]
     */
    private array $cachedParametersInformation;

    /**
     * @return ParameterInformationInterface[]
     */
    public function getParametersInformation(): array
    {
        if (!isset($this->cachedParametersInformation)) {
            $this->cachedParametersInformation = $this->extractParametersInformation();
        }

        return $this->cachedParametersInformation;
    }

    /**
     * @return ParameterInformationInterface[]
     */
    protected function extractParametersInformation(): array
    {
        return [];
    }

    /**
     * @param array $inputParameters
     * @return array
     */
    protected function validateInput(array $inputParameters): array
    {
        $parameters = [];

        $parametersInformation = $this->getParametersInformation();

        foreach ($parametersInformation as $parameterInformation) {
            $name = $parameterInformation->getName();
            $hasDefaultValue = $parameterInformation->hasDefaultValue();
            $nullable = $parameterInformation->isNullable();
            $pipe = $parameterInformation->getPipe();

            $value = null;

            if (array_key_exists($name, $inputParameters)) {
                $value = $inputParameters[$name];
            } else {
                if ($hasDefaultValue) {
                    continue;
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

            $parameters[$name] = $value;
        }

        return $parameters;
    }

    public function pass(mixed $value): mixed
    {
        if (!is_array($value)) {
            throw new RuntimeException("Value passed to method must be an array.");
        }

        $parameters = $this->validateInput($value);

        return $this->handle(...$parameters);
    }
}