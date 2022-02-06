<?php

namespace Invoke\Pipes;

use Invoke\AbstractSingletonPipe;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Invoke;

/**
 * Integer value.
 *
 * Example: <code>123</code>
 */
class IntPipe extends AbstractSingletonPipe
{
    public static IntPipe $instance;

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
            throw new ValidationFailedException($this, $value);
        }

        return $value;
    }

    public function getTypeName(): string
    {
        return "int";
    }
}