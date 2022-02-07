<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Singleton;
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
        return "any";
    }
}