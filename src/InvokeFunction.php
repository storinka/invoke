<?php

namespace Invoke;

use Closure;
use Invoke\Typesystem\CustomTypes\DefaultValueCustomType;
use Invoke\Typesystem\Type;
use Invoke\Typesystem\Typesystem;
use Invoke\Typesystem\Undef;
use ReflectionNamedType;

function normalizeType($type): string
{
    switch ($type) {
        case "int":
        case "integer":
            return Type::Int;
        case "float":
        case "double":
            return Type::Float;
        case "bool":
        case "boolean":
            return Type::Bool;
        case "array":
            return Type::Array;
        case "null":
            return Type::Null;
        case "string":
            return Type::String;
    }

    throw new \RuntimeException("Unsupported built-in type: $type");
}

abstract class InvokeFunction
{
    /**
     * Extension traits
     *
     * @var array $registeredTraits
     */
    private array $registeredTraits = [];

    /**
     * Returns array of params.
     *
     * @return array
     */
    public static function params(): array
    {
        return [];
    }

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
     * Add access verification.
     *
     * @param array $params
     * @return bool
     */
    protected function guard(array $params): bool
    {
        return true;
    }

    /**
     * Invoke the function.
     *
     * @param array $inputParams
     * @return mixed
     */
    public function __invoke(array $inputParams)
    {
        $this->registerTraits();

        $this->executeRegisteredTraits("initialize");

        $reflection = InvokeMachine::configuration("reflection", false);
        $reflectionParameters = null;

        $funParams = static::params();

        if ($reflection) {
            $reflectionClass = new \ReflectionClass($this);
            $reflectionMethod = $reflectionClass->getMethod("handle");
            $reflectionParameters = $reflectionMethod->getParameters();

            foreach ($reflectionParameters as $reflectionParameter) {
                $hasDefault = $reflectionParameter->isDefaultValueAvailable();
                $allowsNull = $reflectionParameter->allowsNull();

                $refParamName = $reflectionParameter->getName();
                $refParamType = $reflectionParameter->getType();

                if ($refParamType instanceof ReflectionNamedType && $refParamType->isBuiltin()) {
                    $refParamType = normalizeType($refParamType->getName());
                }

                if ($hasDefault) {
                    $refParamType = new DefaultValueCustomType($refParamType, $reflectionParameter->getDefaultValue());
                }
                if ($allowsNull) {
                    $refParamType = Type::Null($refParamType);
                }

                if ($refParamName !== "params" && !array_key_exists($refParamName, $funParams)) {
                    $funParams[$refParamName] = $refParamType;
                }
            }
        }

        $validatedParams = [];
        foreach ($funParams as $paramName => $paramType) {
            $value = new Undef();

            if (array_key_exists($paramName, $inputParams)) {
                $value = $inputParams[$paramName];
            }

            $value = Typesystem::validateParam($paramName, $paramType, $value);

            if ($value instanceof Undef) {
                continue;
            }

            $validatedParams[$paramName] = $value;
        }

        $this->prepare($validatedParams);
        $this->executeRegisteredTraits("prepare", [$validatedParams]);

        if (!$this->guard($validatedParams)) {
            throw new InvokeForbiddenException();
        }

        $this->executeRegisteredTraits("guard", [$validatedParams], function ($allowed) {
            if (!$allowed) {
                throw new InvokeForbiddenException();
            }
        });

        if ($reflection) {
            $resolvedParams = [];

            foreach ($reflectionParameters as $reflectionParameter) {
                $methodParamName = $reflectionParameter->getName();

                if ($methodParamName === "params" && !array_key_exists("params", $validatedParams)) {
                    array_push($resolvedParams, $validatedParams);
                } else {
                    array_push($resolvedParams, $validatedParams[$methodParamName]);
                }
            }

            $result = $this->handle(...$resolvedParams);
        } else {
            $result = $this->handle($validatedParams);
        }

        return $result;
    }

    public static function resultType()
    {
        return null;
    }

    private function registerTraits()
    {
        foreach (class_uses($this) as $trait) {
            $this->registeredTraits[] = $trait;
        }
    }

    private function executeRegisteredTraits(string $name, array $functionParams = [], Closure $handler = null)
    {
        foreach ($this->registeredTraits as $trait) {
            $methodName = $name . invoke_get_class_name($trait);

            if (method_exists($this, $methodName)) {
                $result = $this->{$methodName}(...$functionParams);;

                if ($handler) {
                    $handler($result);
                }
            }
        }
    }

    public static function invoke(array $params = [])
    {
        $fun = static::createInstance();

        return $fun($params);
    }

    public static function createInstance(...$args): self
    {
        if (sizeof($args)) {
            return new static(...$args);
        }

        return new static();
    }
}
