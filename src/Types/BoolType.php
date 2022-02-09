<?php

namespace Invoke\Types;

use Invoke\Container\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Invoke;
use Invoke\Support\Singleton;
use Invoke\Type;

/**
 * Boolean type.
 *
 * Example: <code>true</code>
 */
class BoolType implements Type, Singleton
{
    public static BoolType $instance;

    public function pass(mixed $value): mixed
    {
        $type = gettype($value);

        if (Invoke::isInputMode() && Invoke::config("inputMode.convertStrings")) {
            if ($type === "string") {
                if ($value === "true") {
                    return true;
                }

                if ($value === "false") {
                    return false;
                }
            }
        }

        if ($type !== "boolean") {
            throw new InvalidTypeException($this, $value);
        }

        return $value;
    }

    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public static function getName(): string
    {
        return "bool";
    }
}