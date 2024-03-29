<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Invoke;
use Invoke\Stop;
use Invoke\Support\Singleton;
use Invoke\Type;
use Invoke\Utils\Utils;

/**
 * Null type.
 *
 * Example: <code>null</code>
 *
 * @implements Type<mixed, null>
 */
class NullType implements Type, Singleton
{
    public static NullType $instance;

    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        $type = gettype($value);

        $invoke = Container::get(Invoke::class);

        if ($invoke->isInputMode() && $invoke->getConfig("inputMode.convertStrings")) {
            if ($type === "string") {
                if ($value === "NULL") {
                    return null;
                }
            }
        }

        if ($type !== "NULL") {
            throw new InvalidTypeException($this, Utils::getValueTypeName($value));
        }

        return $value;
    }

    public static function invoke_getTypeName(): string
    {
        return "null";
    }

    public static function getInstance(): static
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
