<?php

namespace Invoke\Types;

use Invoke\Support\Singleton;
use Invoke\Type;

/**
 * Any type.
 *
 * Example: <code>"some string value"</code>, <code>1232.21</code>
 *
 * @implements Type<mixed, mixed>
 */
class AnyType implements Type, Singleton
{
    public static AnyType $instance;

    public function run(mixed $value): mixed
    {
        return $value;
    }

    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public static function invoke_getTypeName(): string
    {
        return "any";
    }
}
