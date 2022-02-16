<?php

namespace Invoke\Types;

use Invoke\Exceptions\InvalidTypeException;
use Invoke\Stop;
use Invoke\Support\Singleton;
use Invoke\Type;
use Invoke\Utils\Utils;

/**
 * Array type.
 *
 * Example: <code>[1, 2, 3]</code>
 *
 * @implements Type<mixed, array>
 */
class ArrayType implements Type, Singleton
{
    public static ArrayType $instance;

    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        if (gettype($value) !== "array") {
            throw new InvalidTypeException($this, Utils::getValueTypeName($value));
        }

        return $value;
    }

    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function invoke_getTypeName(): string
    {
        return "array";
    }
}
