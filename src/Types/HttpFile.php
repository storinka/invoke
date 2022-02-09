<?php

namespace Invoke\Types;

class HttpFile implements BinaryType
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