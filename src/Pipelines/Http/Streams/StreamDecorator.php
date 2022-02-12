<?php

namespace Invoke\Pipelines\Http\Streams;

use Psr\Http\Message\StreamInterface;

interface StreamDecorator
{
    public function getStream(): StreamInterface;
}
