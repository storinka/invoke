<?php

namespace Invoke;

use Invoke\Types\TypeWithParams;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use RuntimeException;

/**
 * Abstract method pipe.
 *
 * @method mixed handle() method handler, must be protected or private
 */
abstract class Method extends TypeWithParams
{
    protected static array $extensionTraits;

    public function pass(mixed $input): mixed
    {
        if ($input instanceof Stop) {
            return $input;
        }

        Invoke::setInputMode(true);

        // validate class parameters
        parent::pass($input);

        $reflectionClass = ReflectionUtils::getClass($this::class);

        $handleMethod = $reflectionClass->getMethod("handle");
        if ($handleMethod->isPublic()) {
            throw new RuntimeException("{$reflectionClass->name} \"handle\" method cannot be public.");
        }

        // validate "handle" method parameters
        $methodParameters = $this->_validateParameters(
            $handleMethod->getParameters(),
            $input
        );

        Invoke::setInputMode(false);

        ReflectionUtils::callMethodExtensionsHook($this, "beforeHandle");

        $result = $this->handle(...array_values($methodParameters));

        ReflectionUtils::callMethodExtensionsHook($this, "afterHandle", [$result]);

        return $result;
    }

    public function invoke_getUsedTypes(): array
    {
        $pipes = parent::invoke_getUsedTypes();

        $reflectionClass = ReflectionUtils::getClass($this::class);
        $reflectionMethod = $reflectionClass->getMethod("handle");

        return [...$pipes, ReflectionUtils::extractPipeFromMethodReturnType($reflectionMethod)];
    }

    public static function invoke(array $params = []): mixed
    {
        $method = Container::make(static::class);

        return Pipeline::pass($method, $params);
    }

    public static function invoke_getName(): string
    {
        return Utils::getMethodNameFromClass(static::class);
    }
}
