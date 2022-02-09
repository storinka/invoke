<?php

namespace Invoke\Types;

use Invoke\Container\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Invoke;
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
            static::$instance = Container::getInstance()->get(static::class);
        }

        return static::$instance;
    }

    public static function getName(): string
    {
        return "int";
    }
}