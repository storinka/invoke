<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Singleton;
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
        if (gettype($value) !== "string") {
            throw new InvalidTypeException($this, $value);
        }

        return $value;
    }

    public static function getName(): string
    {
        return "string";
    }

    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = Container::make(static::class);
        }

        return static::$instance;
    }
}