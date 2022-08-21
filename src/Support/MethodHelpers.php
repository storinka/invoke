<?php

namespace Invoke\Support;

use Invoke\Method;
use Invoke\NewMethod\Description\MethodDescriptionInterface;
use Invoke\NewMethod\Description\MethodDescriptionInterfaceImpl;
use Invoke\NewMethod\Information\ParameterInformationInterface;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Validation;

/**
 * @mixin Method
 */
trait MethodHelpers
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
    protected function asInvokeExtractParametersInformation(): array
    {
        $reflectionClass = ReflectionUtils::getClass($this::class);
        $reflectionMethod = $reflectionClass->getMethod("handle");
        $reflectionParameters = $reflectionMethod->getParameters();

        return ReflectionUtils::extractParametersInformation($reflectionParameters);
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