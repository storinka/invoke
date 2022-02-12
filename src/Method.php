<?php

namespace Invoke;

use Invoke\Types\TypeWithParams;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;

/**
 * Abstract method pipe.
 *
 * @template R
 */
abstract class Method extends TypeWithParams
{
    /**
     * Method handler.
     *
     * @return R
     */
    protected abstract function handle();

    public function pass(mixed $input): mixed
    {
        if ($input instanceof Stop) {
            return $input;
        }

        $invoke = Container::get(Invoke::class);

        $invoke->setInputMode(true);

        // validate parameters
        parent::pass($input);

        $invoke->setInputMode(false);

        ReflectionUtils::callMethodExtensionsHook($this, "beforeHandle");

        $result = $this->handle();

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

    public static function invoke_getTypeName(): string
    {
        return Utils::getMethodNameFromClass(static::class);
    }

    /**
     * Invoke the method.
     *
     * @param array $params
     * @return R
     */
    public static function invoke(array $params = []): mixed
    {
        $method = Container::make(static::class);

        return Piping::run($method, $params);
    }
}
