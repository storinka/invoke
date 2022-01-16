<?php

namespace Invoke;

use Closure;
use Invoke\Attributes\MethodExtension;
use Invoke\Exceptions\InvalidParamTypeException;
use Invoke\Exceptions\InvalidParamValueException;
use Invoke\Utils\ReflectionUtils;
use ReflectionClass;
use RuntimeException;

abstract class Method
{
    /**
     * Extension traits
     *
     * @var array $extensionTraits
     */
    private array $extensionTraits = [];

    protected abstract function handle(): AsData|float|int|string|null|array;

    public function __invoke(array $params): AsData|float|int|string|null|array
    {
        $this->registerExtensionTraits();

        $this->callExtensionsMethod("init", [$params]);

        $reflectionClass = new ReflectionClass($this);

        try {
            $params = Typesystem::validateParams(
                ReflectionUtils::reflectionParamsOrPropsToInvoke($reflectionClass->getProperties()),
                $params
            );
        } catch (InvalidParamTypeException $exception) {
            throw new InvalidParamTypeException(
                $exception->getParamName(),
                $exception->getParamType(),
                $exception->getActualType(),
                $exception->getMessage(),
                400
            );
        } catch (InvalidParamValueException $exception) {
            throw new InvalidParamValueException(
                $exception->getParamName(),
                $exception->getParamType(),
                $exception->getValue(),
                $exception->getMessage(),
                400
            );
        }

        foreach ($params as $name => $value) {
            $this->{$name} = $value;
        }

        $this->callExtensionsMethod("beforeHandle", [$params]);

        $result = $this->handle();

        $this->callExtensionsMethod("afterHandle", [$result]);

        return $result;
    }

    private function registerExtensionTraits()
    {
        if (sizeof($this->extensionTraits)) {
            throw new RuntimeException("BUG: extension traits were already registered.");
        }

        foreach (class_uses_deep($this) as $trait) {
            $reflectionClass = new ReflectionClass($trait);

            if ($reflectionClass->getAttributes(MethodExtension::class)) {
                $this->extensionTraits[] = $trait;
            }
        }
    }

    private function callExtensionsMethod(string $name, array $functionParams = [], Closure $handler = null)
    {
        foreach ($this->extensionTraits as $trait) {
            // method + traitClass
            // example:
            // method is "prepare"
            // traitClass is "WithEditPermission"
            // methodName = prepareWithEditPermission
            $methodName = $name . invoke_get_class_name($trait);

            if (method_exists($this, $methodName)) {
                $result = $this->{$methodName}(...$functionParams);

                if ($handler) {
                    $handler($result);
                }
            }
        }
    }
}