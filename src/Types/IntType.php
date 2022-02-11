<?php

namespace Invoke\Types;

use Invoke\Exceptions\InvalidTypeException;
use Invoke\Invoke;
use Invoke\Stop;
use Invoke\Support\Singleton;
use Invoke\Type;

/**
 * Integer value.
 *
 * Example: <code>123</code>
 */
class IntType implements Type, Singleton
{
    public static IntType $instance;

    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        $type = gettype($value);

        if (Invoke::isInputMode() && Invoke::config("inputMode.convertStrings")) {
            if ($type === "string") {
                if (is_numeric($value)) {
                    return intval($value);
                }
            }
        }

        if ($type !== "integer") {
            throw new InvalidTypeException($this, $value);
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

    public static function invoke_getName(): string
    {
        return "int";
    }
}
