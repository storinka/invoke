<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Pipe;
use Invoke\Pipeline;
use Invoke\Stop;
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
        if ($value instanceof Stop) {
            return $value;
        }

        if (is_object($value)) {
            if ($value::class === $this->typeClass) {
                return $value;
            }
        }

        if (is_subclass_of($this->typeClass, Pipe::class)) {
            $newPipe = Container::make($this->typeClass);

            return Pipeline::pass($newPipe, $value);
        }

        throw new InvalidTypeException($this, $value);
    }

    public static function invoke_getName(): string
    {
        return "wrapped";
    }

    public function invoke_getDynamicName(): string
    {
        return $this->typeClass::invoke_getName();
    }

    public function invoke_getUsedTypes(): array
    {
        $pipes = [];

        if (is_subclass_of($this->typeClass, TypeWithParams::class)) {
            return ReflectionUtils::extractPipesFromParamsPipe($this->typeClass);
        }

        // todo: extract from other types

        return $pipes;

    }
}