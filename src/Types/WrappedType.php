<?php

namespace Invoke\Types;

use Invoke\Container\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Pipe;
use Invoke\Support\HasDynamicName;
use Invoke\Support\HasUsedTypes;
use Invoke\Type;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use RuntimeException;

class WrappedType implements Type, HasDynamicName, HasUsedTypes
{
    /** @var class-string<Type> $typeClass */
    public string $typeClass;

    /** @var class-string<Type> $typeClass */
    public function __construct(string $typeClass)
    {
        if (!Utils::isPipeType($typeClass)) {
            throw new RuntimeException("Cannot create wrapped type for \"$typeClass\".");
        }

        $this->typeClass = $typeClass;
    }

    public function pass(mixed $value): mixed
    {
        if (is_object($value)) {
            if ($value::class === $this->typeClass) {
                return $value;
            }
        }

        if (is_subclass_of($this->typeClass, Pipe::class)) {
            $newPipe = Container::getInstance()->make($this->typeClass);

            return $newPipe->pass($value);
        }

        throw new InvalidTypeException($this, $value);
    }

    public static function getName(): string
    {
        return "wrapped";
    }

    public function getDynamicName(): string
    {
        return $this->typeClass::getName();
    }

    public function getUsedTypes(): array
    {
        $pipes = [];

        if (is_subclass_of($this->typeClass, TypeWithParams::class)) {
            return ReflectionUtils::extractPipesFromParamsPipe($this->typeClass);
        }

        // todo: extract from other types

        return $pipes;

    }
}