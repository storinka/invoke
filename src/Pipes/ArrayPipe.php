<?php

namespace Invoke\Pipes;

use Invoke\AbstractSingletonPipe;
use Invoke\Exceptions\ValidationFailedException;

/**
 * Array type.
 *
 * Example: <code>[1, 2, 3]</code>
 */
class ArrayPipe extends AbstractSingletonPipe
{
    public static ArrayPipe $instance;

    public function pass(mixed $value): mixed
    {
        if (gettype($value) !== "array") {
            throw new ValidationFailedException($this, $value);
        }

        return $value;
    }

    public function getTypeName(): string
    {
        return "array";
    }
}