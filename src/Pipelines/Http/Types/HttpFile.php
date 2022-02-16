<?php

namespace Invoke\Pipelines\Http\Types;

use Invoke\Pipelines\Http\Streams\StreamDecorator;
use Invoke\Stop;
use Invoke\Support\BinaryType;
use Psr\Http\Message\StreamInterface;

class HttpFile implements BinaryType, StreamDecorator
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
