<?php

namespace Invoke\Types;

use Invoke\Stop;
use Invoke\Support\Singleton;
use Invoke\Type;

/**
 * Any type.
 *
 * Example: <code>"some string value"</code>, <code>1232.21</code>
 */
class AnyType implements Type, Singleton
{
    public static AnyType $instance;

    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
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

    public static function invoke_getName(): string
    {
        return "any";
    }
}