<?php

namespace Invoke\Types;

use Invoke\Stop;

class HttpFile implements BinaryType
{
    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        return $value;
    }

    public static function getName(): string
    {
        return "file";
    }
}