<?php

namespace Invoke\NewMethod;

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
    /**
     * @var ParameterInformationInterface[]
     */
    private array $cachedParametersInformation;

    /**
     * @return ParameterInformationInterface[]
     */
    public function asInvokeGetParametersInformation(): array
    {
        if (!isset($this->cachedParametersInformation)) {
            $this->cachedParametersInformation = $this->asInvokeExtractParametersInformation();
        }

        return $this->cachedParametersInformation;
    }

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
    protected function asInvokeExtractParametersInformation(): array
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