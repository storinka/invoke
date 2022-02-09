<?php

namespace Invoke\Types;

use Invoke\Type;
use Psr\Http\Message\StreamInterface;

/**
 * Binary data type.
 *
 * @see HttpFile
 */
interface BinaryType extends Type
{
    public function getType(): string;

    public function getStream(): StreamInterface;
}