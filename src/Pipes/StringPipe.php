<?php

namespace Invoke\Pipes;

use Invoke\AbstractSingletonPipe;
use Invoke\Exceptions\ValidationFailedException;

/**
 * String type.
 *
 * Example: <code>\"Diana\"</code>
 */
class StringPipe extends AbstractSingletonPipe
{
    public static StringPipe $instance;

    public function pass(mixed $value): mixed
    {
        if (gettype($value) !== "string") {
            throw new ValidationFailedException($this, $value);
        }

        return $value;
    }

    public function getTypeName(): string
    {
        return "string";
    }
}