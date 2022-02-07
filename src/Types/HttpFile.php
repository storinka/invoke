<?php

namespace Invoke\Types;

use Invoke\Binary;

class HttpFile implements Binary
{
    public function pass(mixed $value): mixed
    {
        return $value;
    }

    public static function getName(): string
    {
        return "file";
    }
}