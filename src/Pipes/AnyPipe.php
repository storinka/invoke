<?php

namespace Invoke\Pipes;

use Invoke\AbstractSingletonPipe;

/**
 * Any type.
 *
 * Example: <code>"some string value"</code>, <code>1232.21</code>
 */
class AnyPipe extends AbstractSingletonPipe
{
    public static AnyPipe $instance;

    public function pass(mixed $value): mixed
    {
        return $value;
    }

    public function getTypeName(): string
    {
        return "any";
    }
}