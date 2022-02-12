<?php

namespace Invoke\Types;

use Invoke\Exceptions\InvalidTypeException;
use Invoke\Meta\Singleton;
use Invoke\Stop;
use Invoke\Type;

/**
 * String type.
 *
 * Example: <code>\"Diana\"</code>
 */
class StringType implements Type, Singleton
{
    public static StringType $instance;

    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        if (gettype($value) !== "string") {
            throw new InvalidTypeException($this, $value);
        }

        return $value;
    }

    public static function invoke_getTypeName(): string
    {
        return "string";
    }

    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
