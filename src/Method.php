<?php

namespace Invoke;

use Invoke\Container\Container;
use Invoke\Types\TypeWithParams;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use ReflectionClass;
use RuntimeException;

/**
 * Abstract method pipe.
 *
 * @method mixed handle() method handler, must be protected or private
 */
abstract class Method extends TypeWithParams
{
    public function pass(mixed $input): mixed
    {
        if ($input instanceof Stop) {
            return $input;
        }

        Invoke::setInputMode(true);

        parent::pass($input);

        $reflectionClass = ReflectionUtils::getClass($this::class);

        $handleMethod = $reflectionClass->getMethod("handle");
        if ($handleMethod->isPublic()) {
            throw new RuntimeException("{$reflectionClass->name} \"handle\" method cannot be public.");
        }

        $parameters = $this->_validateParameters(
            $handleMethod->getParameters(),
            $input
        );

        Invoke::setInputMode(false);

        return $this->handle(...array_values($parameters));
    }

    public function getUsedTypes(): array
    {
        $pipes = parent::getUsedTypes();

        $reflectionClass = ReflectionUtils::getClass($this::classs);
        $reflectionMethod = $reflectionClass->getMethod("handle");

        return [...$pipes, ReflectionUtils::extractPipeFromMethodReturnType($reflectionMethod)];
    }

    public static function invoke(array $params = []): mixed
    {
        $method = Container::getInstance()->make(static::class);

        return $method->pass($params);
    }

    public static function getName(): string
    {
        return Utils::getMethodNameFromClass(static::class);
    }
}