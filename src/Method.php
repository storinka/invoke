<?php

namespace Invoke;

use Invoke\Attributes\NotParameter;
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
    #[NotParameter]
    protected array $handleParameters = [];

    /**
     * @inheritDoc
     */
    public function pass(mixed $input): mixed
    {
        if ($input instanceof Stop) {
            return $input;
        }

        // call "beforeValidation" hook on extensions
        Invoke::callMethodExtensionsHook($this, "beforeValidation");

        // get current instance of invoke from container
        $invoke = Container::get(Invoke::class);

        // enable input mode
        $invoke->setInputMode(true);

        $usePropertiesAsParameters = $invoke->getConfig("methods.usePropertiesAsParameters", true);

        if ($usePropertiesAsParameters) {
            // validate parameters
            parent::pass($input);
        }

        $this->handleParameters = $this->validateHandleMethod($input);

        // disable input mode
        $invoke->setInputMode(false);

        // call "beforeHandle" hook on extensions
        Invoke::callMethodExtensionsHook($this, "beforeHandle");

        // handle the method
        $result = $this->handle(...$this->handleParameters);

        // call "afterHandle" hook on extensions
        Invoke::callMethodExtensionsHook($this, "afterHandle", [$result]);

        return $result;
    }

    private function validateHandleMethod(array $input): array
    {
        $reflectionClass = ReflectionUtils::getClass($this::class);
        $handleMethod = $reflectionClass->getMethod("handle");

        if (!$handleMethod->isProtected()) {
            throw new \RuntimeException("\"handle\" method of {$reflectionClass->name} must be protected.");
        }

        return $this->validate($handleMethod->getParameters(), $input, true);
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

    public function __get(string $name)
    {
        return $this->handleParameters[$name];
    }
}
