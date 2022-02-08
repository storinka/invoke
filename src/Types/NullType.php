<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Invoke;
use Invoke\Singleton;
use Invoke\Type;

/**
 * Null type.
 *
 * Example: <code>null</code>
 */
class NullType implements Type, Singleton
{
    public static NullType $instance;

    public function pass(mixed $value): mixed
    {
        $type = gettype($value);

        if (Invoke::isInputMode() && Invoke::config("inputMode.convertStrings")) {
            if ($type === "string") {
                if ($value === "NULL") {
                    return null;
                }
            }
        }

        if ($type !== "NULL") {
            throw new InvalidTypeException($this, $value);
        }

        return $value;
    }

    public static function getName(): string
    {
        return "null";
    }

    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = Container::getInstance()->get(static::class);
        }

        return static::$instance;
    }
}