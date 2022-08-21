<?php

namespace Invoke\Types;

use Invoke\Container;
use Invoke\Exceptions\InvalidTypeException;
use Invoke\Invoke;
use Invoke\Support\Singleton;
use Invoke\Type;
use Invoke\Utils\Utils;

/**
 * Integer value.
 *
 * Example: <code>123</code>
 *
 * @implements Type<mixed, int>
 */
class IntType implements Type, Singleton
{
    public static IntType $instance;

    public function run(mixed $value): mixed
    {

        $type = gettype($value);

        $invoke = Container::get(Invoke::class);

        if ($invoke->isInputMode() && $invoke->getConfig("inputMode.convertStrings")) {
            if ($type === "string") {
                if (is_numeric($value)) {
                    return intval($value);
                }
            }
        }

        if ($type !== "integer") {
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
        return "int";
    }
}
