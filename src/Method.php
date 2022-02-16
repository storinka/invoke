<?php

namespace Invoke;

use Invoke\Support\TypeWithParams;
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

    /**
     * @inheritDoc
     */
    public function pass(mixed $input): mixed
    {
        if ($input instanceof Stop) {
            return $input;
        }

        // call "beforeValidation" hooks on extensions
        ReflectionUtils::callMethodExtensionsHook($this, "beforeValidation");

        // get current instance of invoke from container
        $invoke = Container::get(Invoke::class);

        // enable input mode
        $invoke->setInputMode(true);

        // validate parameters
        parent::pass($input);

        // disable input mode
        $invoke->setInputMode(false);

        // call "beforeHandle" hooks on extensions
        ReflectionUtils::callMethodExtensionsHook($this, "beforeHandle");

        // handle the method
        $result = $this->handle();

        // call "afterHandle" hooks on extensions
        ReflectionUtils::callMethodExtensionsHook($this, "afterHandle", [$result]);

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function invoke_getUsedTypes(): array
    {
        $pipes = parent::invoke_getUsedTypes();

        $reflectionClass = ReflectionUtils::getClass($this::class);
        $reflectionMethod = $reflectionClass->getMethod("handle");

        return [...$pipes, ReflectionUtils::extractPipeFromMethodReturnType($reflectionMethod)];
    }

    /**
     * @inheritDoc
     */
    public function shouldRequireTypeName(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function shouldReturnTypeName(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
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
