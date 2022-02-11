<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Invoke;
use Invoke\Stop;
use Invoke\Support\Singleton;
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
        if ($value instanceof Stop) {
            return $value;
        }
        
        $type = gettype($value);

        $invoke = Container::get(Invoke::class);

        if ($invoke->isInputMode() && $invoke->getConfig("inputMode.convertStrings")) {
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
            static::$instance = new static;
        }

        return static::$instance;
    }

    public static function invoke_getName(): string
    {
        return "float";
    }
}