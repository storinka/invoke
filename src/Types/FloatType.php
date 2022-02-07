<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Invoke;
use Invoke\Singleton;
use Invoke\Type;

/**
 * Float type.
 *
 * Example: <code>3.14</code>
 */
class FloatType implements Type, Singleton
{
    public static FloatType $instance;

    public function pass(mixed $value): mixed
    {
        $type = gettype($value);

        if (Invoke::isInputMode() && Invoke::config("inputMode.convertStrings")) {
            if ($type === "string") {
                if (is_numeric($value)) {
                    return floatval($value);
                }
            }
        }

        if ($type === "integer") {
            return floatval($value);
        }

        if ($type !== "double") {
            throw new InvalidTypeException($this, $value);
        }

        return $value;
    }

    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = Container::make(static::class);
        }

        return static::$instance;
    }

    public static function getName(): string
    {
        return "float";
    }
}