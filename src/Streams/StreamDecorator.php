<?php

namespace Invoke\Streams;

use Psr\Http\Message\StreamInterface;

interface StreamDecorator
{
    public function getStream(): StreamInterface;
}
