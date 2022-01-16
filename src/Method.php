<?php

namespace Invoke;

use Closure;
use Invoke\Attributes\TraitExtension;
use Invoke\Exceptions\InvalidParamTypeException;
use Invoke\Exceptions\InvalidParamValueException;
use Invoke\Utils\ReflectionUtils;
use ReflectionClass;

abstract class Method
{
    /**
     * Extension traits
     *
     * @var string[] $extensionTraits
     */
    private array $extensionTraits = [];

    /**
     * Method extensions
     *
     * @var MethodExtension[] $methodExtensions
     */
    private array $methodExtensions = [];

    protected abstract function handle(): AsData|float|int|string|null|array;

    public function __invoke(array $params): AsData|float|int|string|null|array
    {
        $this->registerExtensionTraits();

        $this->callExtensionsHook("init", [$this]);
        Invoke::callExtensionsHook("methodInit", [$this]);

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

        $this->callExtensionsHook("beforeHandle", [$params]);
        Invoke::callExtensionsHook("methodBeforeHandle", [$this, $params]);

        $result = $this->handle();

        $this->callExtensionsHook("afterHandle", [$result]);
        Invoke::callExtensionsHook("methodAfterHandle", [$this, $result]);

        return $result;
    }

    private function registerExtensionTraits()
    {
        if (sizeof($this->extensionTraits)) {
            throw new RuntimeException("BUG: extension traits were already registered.");
        }

        foreach (class_uses_deep($this) as $trait) {
            $reflectionClass = new ReflectionClass($trait);

            if ($reflectionClass->getAttributes(TraitExtension::class)) {
                $this->extensionTraits[] = $trait;
            }
        }

        foreach ((new ReflectionClass($this))->getAttributes() as $attribute) {
            if (is_subclass_of($attribute->getName(), MethodExtension::class)) {
                $this->methodExtensions[] = $attribute->newInstance();
            }
        }
    }

    private function callExtensionsHook(string $name, array $params = [], Closure $handler = null)
    {
        foreach ($this->extensionTraits as $trait) {
            // method + traitClass
            // example:
            // method is "prepare"
            // traitClass is "WithEditPermission"
            // methodName = prepareWithEditPermission
            $methodName = $name . invoke_get_class_name($trait);

            if (method_exists($this, $methodName)) {
                $result = $this->{$methodName}(...$params);

                if ($handler) {
                    $handler($result);
                }
            }
        }

        foreach ($this->methodExtensions as $extension) {
            if (method_exists($extension, $name)) {
                $result = $extension->{$name}(...$params);

                if ($handler) {
                    $handler($result);
                }
            }
        }
    }
}