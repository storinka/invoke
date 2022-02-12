<?php

namespace Invoke\Toolkit\Types;

use Invoke\Stop;
use Invoke\Types\BinaryType;
use Psr\Http\Message\StreamInterface;

class InputFile implements BinaryType
{
    public function pass(mixed $value): mixed
    {
        if ($value instanceof Stop) {
            return $value;
        }

        return $value;
    }

    public static function invoke_getTypeName(): string
    {
        return "file";
    }

    public function getStream(): StreamInterface
    {
        // TODO: Implement getStream() method.
    }
}
