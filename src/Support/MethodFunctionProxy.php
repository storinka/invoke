<?php

namespace Invoke\Support;

use Invoke\Method;
use Invoke\NewMethod\Information\ParameterInformationInterface;
use Invoke\Utils\ReflectionUtils;
use ReflectionFunction;
use RuntimeException;

class MethodFunctionProxy extends Method
{
    public function __construct(protected $functionName)
    {
        if (!function_exists($this->functionName)) {
            throw new RuntimeException('Function does not exist.');
        }
    }

    /**
     * @return ParameterInformationInterface[]
     */
    protected function asInvokeExtractParametersInformation(): array
    {
        $reflectionFunction = new ReflectionFunction($this->functionName);
        $reflectionParameters = $reflectionFunction->getParameters();

        return ReflectionUtils::extractParametersInformation($reflectionParameters);
    }

    protected function handle(array $CURRENT_PARAMETERS)
    {
        return ReflectionUtils::invokeFunction(
            $this->functionName,
            $CURRENT_PARAMETERS
        );
    }
}