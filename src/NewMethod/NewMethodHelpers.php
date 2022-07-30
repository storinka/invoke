<?php

namespace Invoke\NewMethod;

use Invoke\NewData\WithCachedParametersInformation;
use Invoke\NewMethod\Description\MethodDescriptionInterface;
use Invoke\NewMethod\Description\MethodDescriptionInterfaceImpl;
use Invoke\NewMethod\Information\ParameterInformation;
use Invoke\NewMethod\Information\ParameterInformationInterface;
use Invoke\Pipe;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Validation;

/**
 * @mixin NewMethod
 */
trait NewMethodHelpers
{
    use WithCachedParametersInformation;

    /**
     * @return MethodDescriptionInterface
     */
    public function asInvokeGetMethodDescription(): MethodDescriptionInterface
    {
        return new MethodDescriptionInterfaceImpl($this);
    }

    /**
     * @param array $inputParameters
     * @return array
     */
    protected function asInvokeValidateInputParameters(array $inputParameters): array
    {
        $parametersInformation = $this->asInvokeGetParametersInformation();

        return Validation::validateParametersInformation(
            $parametersInformation,
            $inputParameters
        );
    }

    /**
     * @return ParameterInformationInterface[]
     */
    private function asInvokeExtractParametersInformation(): array
    {
        $parameters = [];

        $reflectionClass = ReflectionUtils::getClass($this::class);
        $reflectionMethod = $reflectionClass->getMethod("handle");
        $reflectionParameters = $reflectionMethod->getParameters();

        foreach ($reflectionParameters as $reflectionParameter) {
            $type = $reflectionParameter->getType();

            $name = $reflectionParameter->getName();
            $pipe = ReflectionUtils::extractPipeFromReflectionType($type);
            $hasDefaultValue = $reflectionParameter->isDefaultValueAvailable();
            $defaultValue = $hasDefaultValue ? $reflectionParameter->getDefaultValue() : null;
            $nullable = $type->allowsNull();

            $validators = [];

            foreach ($reflectionParameter->getAttributes() as $attribute) {
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

    protected function asInvokeAddParameterInformation(ParameterInformationInterface $parameterInformation,
                                                       bool                          $force = false): void
    {
        foreach ($this->asInvokeGetParametersInformation() as $information) {
            if ($information->getName() === $parameterInformation->getName()) {
                if ($force) {
                    $this->cachedParametersInformation = array_filter($this->cachedParametersInformation, fn($p) => $p->getName() !== $parameterInformation->getName());
                    break;
                } else {
                    return;
                }
            }
        }

        $this->cachedParametersInformation[] = $parameterInformation;
    }
}