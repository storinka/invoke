<?php

namespace Invoke\Pipes;

use Invoke\AbstractSingletonPipe;
use Invoke\Exceptions\ValidationFailedException;
use Invoke\Invoke;

/**
 * Null type.
 *
 * Example: <code>null</code>
 */
class NullPipe extends AbstractSingletonPipe
{
    public static NullPipe $instance;

    public function pass(mixed $value): mixed
    {
        $type = gettype($value);

        if (Invoke::isInputMode() && Invoke::config("inputMode.convertStrings")) {
            if ($type === "string") {
                if ($value === "NULL") {
                    return null;
                }
            }
        }

        if ($type !== "NULL") {
            throw new ValidationFailedException($this, $value);
        }

        return $value;
    }

    public function getTypeName(): string
    {
        return "null";
    }
}