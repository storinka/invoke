<?php

namespace Invoke\Pipes;

use Invoke\AbstractSingletonPipe;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Invoke;

/**
 * Boolean type.
 *
 * Example: <code>true</code>
 */
class BoolPipe extends AbstractSingletonPipe
{
    public static BoolPipe $instance;

    public function pass(mixed $value): mixed
    {
        $type = gettype($value);

        if (Invoke::isInputMode() && Invoke::config("inputMode.convertStrings")) {
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
            throw new ValidationFailedException($this, $value);
        }

        return $value;
    }

    public function getTypeName(): string
    {
        return "bool";
    }
}