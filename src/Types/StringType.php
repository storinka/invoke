<?php

namespace Invoke\Types;

use Invoke\Exceptions\InvalidTypeException;
use Invoke\Stop;
use Invoke\Support\Singleton;
use Invoke\Type;
use Invoke\Utils\Utils;

/**
 * String type.
 *
 * Example: <code>\"Diana\"</code>
 *
 * @implements Type<mixed, string>
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
            throw new InvalidTypeException($this, Utils::getValueTypeName($value));
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
