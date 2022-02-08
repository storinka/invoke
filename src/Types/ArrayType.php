<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Singleton;
use Invoke\Type;

/**
 * Array type.
 *
 * Example: <code>[1, 2, 3]</code>
 */
class ArrayType implements Type, Singleton
{
    public static ArrayType $instance;

    public function pass(mixed $value): mixed
    {
        if (gettype($value) !== "array") {
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
        return "array";
    }
}