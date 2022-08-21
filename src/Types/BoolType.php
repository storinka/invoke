<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Invoke;
use Invoke\Support\Singleton;
use Invoke\Type;
use Invoke\Utils\Utils;

/**
 * Boolean type.
 *
 * Example: <code>true</code>
 *
 * @implements Type<mixed, bool>
 */
class BoolType implements Type, Singleton
{
    public static BoolType $instance;

    public function run(mixed $value): mixed
    {
        $type = gettype($value);

        $invoke = Container::get(Invoke::class);

        if ($invoke->isInputMode() && $invoke->getConfig("inputMode.convertStrings")) {
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
            throw new InvalidTypeException($this, Utils::getValueTypeName($value));
        }

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
        return "bool";
    }
}
