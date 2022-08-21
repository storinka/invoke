<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Pipe;
use Invoke\Piping;
use Invoke\Support\HasDynamicTypeName;
use Invoke\Support\HasUsedTypes;
use Invoke\Support\TypeWithParams;
use Invoke\Type;
use Invoke\Utils\ReflectionUtils;
use Invoke\Utils\Utils;
use RuntimeException;

/**
 * @template T of Type
 */
class WrappedType implements Type, HasDynamicTypeName, HasUsedTypes
{
    /** @var class-string<T> $typeClass */
    public string $typeClass;

    /** @var class-string<T> $typeClass */
    public function __construct(string $typeClass)
    {
        if (!Utils::isPipeType($typeClass)) {
            throw new RuntimeException("Cannot create wrapped type for \"$typeClass\".");
        }

        $this->typeClass = $typeClass;
    }

    public function run(mixed $value): mixed
    {

        if (is_object($value)) {
            if ($value::class === $this->typeClass) {
                return $value;
            }
        }

        if (is_subclass_of($this->typeClass, Pipe::class)) {
            $newPipe = Container::make($this->typeClass);

            return Piping::run($newPipe, $value);
        }

        throw new InvalidTypeException($this, Utils::getValueTypeName($value));
    }

    public static function invoke_getTypeName(): string
    {
        return "wrapped";
    }

    public function invoke_getDynamicTypeName(): string
    {
        return $this->typeClass::invoke_getTypeName();
    }

    public function invoke_getUsedTypes(): array
    {
        $pipes = [];

        if (is_subclass_of($this->typeClass, TypeWithParams::class)) {
            return ReflectionUtils::extractUsedPipesFromParamsPipe($this->typeClass);
        }

        // todo: extract from other types

        return $pipes;
    }
}
