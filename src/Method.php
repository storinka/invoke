<?php

namespace Invoke;

use Invoke\Pipes\ParamsPipe;
use Invoke\Utils\ReflectionUtils;
use ReflectionClass;

abstract class Method extends ParamsPipe
{
    protected abstract function handle();

    public function pass(mixed $input): mixed
    {
        Invoke::setInputMode(true);

        parent::pass($input);

        Invoke::setInputMode(false);

        return $this->handle();
    }

    public function getTypeName(): string
    {
        return Utils::getMethodNameFromClass(static::class);
    }

    public function getUsedPipes(): array
    {
        $pipes = parent::getUsedPipes();

        $reflectionClass = new ReflectionClass($this);
        $reflectionMethod = $reflectionClass->getMethod("handle");

        return [...$pipes, ReflectionUtils::extractPipeFromReturnType($reflectionMethod)];
    }
}