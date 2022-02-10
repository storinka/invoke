<?php

namespace Invoke\Types;

use Invoke\Stop;
use Psr\Http\Message\StreamInterface;

class HttpFile implements BinaryType
{
    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        return $value;
    }

    public static function invoke_getName(): string
    {
        return "file";
    }

    public function getType(): string
    {
        // TODO: Implement getType() method.
    }

    public function getStream(): StreamInterface
    {
        // TODO: Implement getStream() method.
    }
}