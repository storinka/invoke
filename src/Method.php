<?php

namespace Invoke;

use Invoke\Types\TypeWithParams;
use Invoke\Utils\ReflectionUtils;
use ReflectionClass;

/**
 * Abstract method pipe.
 */
abstract class Method extends TypeWithParams
{
    protected abstract function handle();

    public function pass(mixed $input): mixed
    {
        Invoke::setInputMode(true);

        parent::pass($input);

        Invoke::setInputMode(false);

        return $this->handle();
    }

    public function getUsedTypes(): array
    {
        $pipes = parent::getUsedTypes();

        $reflectionClass = new ReflectionClass($this);
        $reflectionMethod = $reflectionClass->getMethod("handle");

        return [...$pipes, ReflectionUtils::extractPipeFromMethodReturnType($reflectionMethod)];
    }
}