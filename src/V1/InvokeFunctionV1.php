<?php

namespace Invoke\V1;

use Closure;
use Invoke\InvokeForbiddenException;
use Invoke\Typesystem\Typesystem;
use Invoke\V1\Typesystem\Utils\ReflectionUtils;
use ReflectionClass;
use RuntimeException;

abstract class InvokeFunctionV1 implements InvokeSubject
{
    /**
     * Extension traits
     *
     * @var array $registeredTraits
     */
    private array $registeredTraits = [];

    /**
     * Prepare function to invocation.
     *
     * @param array $params
     */
    protected function prepare(array $params)
    {
        //
    }

    /**
     * Check if it is allowed to invoke the function.
     *
     * @param array $params
     * @return bool
     */
    protected function authorize(array $params): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public static function params(): array
    {
        return [];
    }

    // implementation

    public function __invoke(array $inputParams)
    {
        $this->registerTraits();

        $this->executeTraits("initialize");

        $reflectionClass = new ReflectionClass($this);

        $params = ReflectionUtils::inspectInvokeFunctionReflectionClassParams($reflectionClass);

        $validatedParams = [];
        foreach ($params as $paramName => $paramType) {
            $value = $inputParams[$paramName] ?? null;

            $value = Typesystem::validateParam($paramName, $paramType, $value);

            $validatedParams[$paramName] = $value;
        }

        $this->executeTraits("prepare", [$validatedParams]);
        $this->prepare($validatedParams);

        $this->executeTraits("authorize", [$validatedParams], function ($allowed) {
            if (!$allowed) {
                throw new InvokeForbiddenException();
            }
        });
        if (!$this->authorize($validatedParams)) {
            throw new InvokeForbiddenException();
        }

        // final handling

        $neededParams = [];

        $reflectionMethod = $reflectionClass->getMethod("handle");
        $reflectionParameters = $reflectionMethod->getParameters();

        // loop though all handle method params
        // to get needed params
        foreach ($reflectionParameters as $reflectionParameter) {
            $refParamName = $reflectionParameter->getName();

            if ($refParamName === "params" && !array_key_exists("params", $validatedParams)) {
                array_push($neededParams, $validatedParams);
            } else {
                array_push($neededParams, $validatedParams[$refParamName]);
            }
        }

        return $this->handle(...$neededParams);
    }

    private function registerTraits()
    {
        if (sizeof($this->registeredTraits)) {
            throw new RuntimeException("BUG: traits were already registered.");
        }

        foreach (class_uses($this) as $trait) {
            $this->registeredTraits[] = $trait;
        }
    }

    private function executeTraits(string $name, array $functionParams = [], Closure $handler = null)
    {
        foreach ($this->registeredTraits as $trait) {
            // method + traitClass
            // example:
            // method is "prepare"
            // traitClass is "WithEditPermission"
            // methodName = prepareWithEditPermission
            $methodName = $name . invoke_get_class_name($trait);

            if (method_exists($this, $methodName)) {
                $result = $this->{$methodName}(...$functionParams);;

                if ($handler) {
                    $handler($result);
                }
            }
        }
    }
}
