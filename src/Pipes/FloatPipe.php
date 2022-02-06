<?php

namespace Invoke\Pipes;

use Invoke\AbstractSingletonPipe;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Invoke;

/**
 * Float type.
 *
 * Example: <code>3.14</code>
 */
class FloatPipe extends AbstractSingletonPipe
{
    public static FloatPipe $instance;

    public function pass(mixed $value): mixed
    {
        $type = gettype($value);

        if (Invoke::isInputMode() && Invoke::config("inputMode.convertStrings")) {
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
            throw new ValidationFailedException($this, $value);
        }

        return $value;
    }

    public function getTypeName(): string
    {
        return "float";
    }
}