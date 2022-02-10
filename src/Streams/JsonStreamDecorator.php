<?php

namespace Invoke\Streams;

use Psr\Http\Message\StreamInterface;

class JsonStreamDecorator implements StreamDecorator
{
    public function __construct(public readonly StreamInterface $stream)
    {
    }

    /**
     * @return StreamInterface
     */
    public function getStream(): StreamInterface
    {
        return $this->stream;
    }
}